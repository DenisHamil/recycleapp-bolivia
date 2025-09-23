<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Donation;
use App\Models\Notification;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        return $user->role === 'collector'
            ? $this->collector($request)
            : $this->donor($request);
    }

    public function donor(Request $request)
    {
        [$donorsLeaderboard, $collectorsLeaderboard, $minReviews, $perPage] =
            $this->leaderboards($request);

        return view('donor.ranking', compact(
            'donorsLeaderboard',
            'collectorsLeaderboard',
            'minReviews',
            'perPage'
        ));
    }

    public function collector(Request $request)
    {
        [$donorsLeaderboard, $collectorsLeaderboard, $minReviews, $perPage] =
            $this->leaderboards($request);

        return view('collector.ranking.index', compact(
            'donorsLeaderboard',
            'collectorsLeaderboard',
            'minReviews',
            'perPage'
        ));
    }

    private function leaderboards(Request $request)
    {
        $minReviews = max(1, (int) $request->get('min_reviews', 1));
        $allowedSizes = [10, 15, 25, 50];
        $perPageReq = (int) $request->get('per_page', 10);
        $perPage = in_array($perPageReq, $allowedSizes, true) ? $perPageReq : 10;

        $finalizedFilter = function ($q) {
            $q->where('d.state', 'completed')
              ->where('d.confirmed_by_collector', 1)
              ->whereNotNull('d.finalized_at');
        };

        $base = DB::table('ratings as r')
            ->join('donations as d', 'd.id', '=', 'r.donation_id')
            ->join('users as u', 'u.id', '=', 'r.to_user_id')
            ->whereBetween('r.stars', [1, 5])
            ->where($finalizedFilter);

        $donorsLeaderboard = (clone $base)
            ->where('u.role', 'donor')
            ->select(
                'u.id',
                'u.first_name',
                'u.last_name',
                'u.profile_image_path',
                DB::raw('AVG(r.stars) as avg_stars'),
                DB::raw('COUNT(r.id) as reviews_count'),
                DB::raw('COUNT(DISTINCT r.donation_id) as completed_count')
            )
            ->groupBy('u.id', 'u.first_name', 'u.last_name', 'u.profile_image_path')
            ->havingRaw('COUNT(r.id) >= ?', [$minReviews])
            ->orderByDesc('avg_stars')
            ->orderByDesc('reviews_count')
            ->orderBy('u.first_name')
            ->paginate($perPage, ['*'], 'donors_page')
            ->appends($request->query());

        $collectorsLeaderboard = (clone $base)
            ->where('u.role', 'collector')
            ->select(
                'u.id',
                'u.first_name',
                'u.last_name',
                'u.profile_image_path',
                DB::raw('AVG(r.stars) as avg_stars'),
                DB::raw('COUNT(r.id) as reviews_count'),
                DB::raw('COUNT(DISTINCT d.id) as completed_count')
            )
            ->groupBy('u.id', 'u.first_name', 'u.last_name', 'u.profile_image_path')
            ->havingRaw('COUNT(r.id) >= ?', [$minReviews])
            ->orderByDesc('avg_stars')
            ->orderByDesc('reviews_count')
            ->orderBy('u.first_name')
            ->paginate($perPage, ['*'], 'collectors_page')
            ->appends($request->query());

        return [$donorsLeaderboard, $collectorsLeaderboard, $minReviews, $perPage];
    }

    public function storeByDonor(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'donation_id' => 'required|uuid|exists:donations,id',
            'stars'       => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string|max:500',
        ]);

        $donation = Donation::where('id', $data['donation_id'])
            ->where('donor_id', $user->id)
            ->firstOrFail();

        if (!$this->isFinalized($donation)) {
            return back()->withErrors('La donación aún no está finalizada.');
        }

        if (!$donation->collector_id) {
            return back()->withErrors('Esta donación no tiene recolector asignado.');
        }

        $exists = DB::table('ratings')
            ->where('donation_id', $donation->id)
            ->where('from_user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors('Ya calificaste esta donación.');
        }

        DB::transaction(function () use ($user, $donation, $data) {
            DB::table('ratings')->insert([
                'id'            => (string) Str::uuid(),
                'from_user_id'  => $user->id,
                'to_user_id'    => $donation->collector_id,
                'donation_id'   => $donation->id,
                'stars'         => (int) $data['stars'],
                'comment'       => $data['comment'] ?? null,
                'created_at'    => now(),
            ]);

            Notification::create([
                'id'         => (string) Str::uuid(),
                'user_id'    => $donation->collector_id,
                'title'      => '⭐ Nueva calificación recibida',
                'message'    => "Te calificaron con {$data['stars']} estrella(s) en una donación finalizada.",
                'type'       => 'rating.received',
                'related_id' => $donation->id,
                'is_read'    => false,
                'created_at' => now(),
            ]);

            Notification::where('type', 'rating.request')
                ->where('user_id', $user->id)
                ->where('related_id', $donation->id)
                ->delete();

            DB::table('activity_log')->insert([
                'id'              => (string) Str::uuid(),
                'user_id'         => $user->id,
                'action_type'     => 'rating.created',
                'reference_table' => 'donations',
                'reference_id'    => $donation->id,
                'detail'          => json_encode(['stars' => (int) $data['stars'], 'by' => 'donor']),
                'created_at'      => now(),
            ]);
        });

        return redirect()->route('donor.notifications.index')
            ->with('success', '¡Gracias! Tu calificación fue registrada.');
    }

    public function storeByCollector(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'donation_id' => 'required|uuid|exists:donations,id',
            'stars'       => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string|max:500',
        ]);

        $donation = Donation::where('id', $data['donation_id'])
            ->where('collector_id', $user->id)
            ->firstOrFail();

        if (!$this->isFinalized($donation)) {
            return back()->withErrors('La donación aún no está finalizada.');
        }

        $exists = DB::table('ratings')
            ->where('donation_id', $donation->id)
            ->where('from_user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors('Ya calificaste esta donación.');
        }

        DB::transaction(function () use ($user, $donation, $data) {
            DB::table('ratings')->insert([
                'id'            => (string) Str::uuid(),
                'from_user_id'  => $user->id,
                'to_user_id'    => $donation->donor_id,
                'donation_id'   => $donation->id,
                'stars'         => (int) $data['stars'],
                'comment'       => $data['comment'] ?? null,
                'created_at'    => now(),
            ]);

            Notification::create([
                'id'         => (string) Str::uuid(),
                'user_id'    => $donation->donor_id,
                'title'      => '⭐ Nueva calificación recibida',
                'message'    => "Te calificaron con {$data['stars']} estrella(s) en una donación finalizada.",
                'type'       => 'rating.received',
                'related_id' => $donation->id,
                'is_read'    => false,
                'created_at' => now(),
            ]);

            Notification::where('type', 'rating.request')
                ->where('user_id', $user->id)
                ->where('related_id', $donation->id)
                ->delete();

            DB::table('activity_log')->insert([
                'id'              => (string) Str::uuid(),
                'user_id'         => $user->id,
                'action_type'     => 'rating.created',
                'reference_table' => 'donations',
                'reference_id'    => $donation->id,
                'detail'          => json_encode(['stars' => (int) $data['stars'], 'by' => 'collector']),
                'created_at'      => now(),
            ]);
        });

        return redirect()->route('collector.notifications.index')
            ->with('success', '¡Gracias! Tu calificación fue registrada.');
    }

    private function isFinalized(Donation $d): bool
    {
        return $d->state === 'completed'
            && (int) $d->confirmed_by_collector === 1
            && !empty($d->finalized_at);
    }
}
