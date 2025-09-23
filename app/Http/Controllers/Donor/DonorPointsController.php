<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\RewardRedeemedMail;
use App\Mail\RewardRedeemedAdminMail; // 👈 Nuevo Mailable para admin
use App\Models\RewardStore;
use App\Models\UserReward;
use App\Models\UserPoint;

class DonorPointsController extends Controller
{
    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $totalPoints = $user->total_points
            ?? (int) UserPoint::where('user_id', $user->id)->sum('points');

        $currentLevel = $user->current_level->level ?? $user->current_level ?? null;
        if (!$currentLevel) {
            $lvl = DB::table('user_levels')
                ->where('min_points', '<=', $totalPoints)
                ->orderByDesc('min_points')
                ->first();
            $currentLevel = $lvl->level ?? 'bronze';
        }

        $nextLevel = $user->next_level->level ?? $user->next_level ?? null;
        $pointsToNextLevel = $user->points_to_next_level ?? null;
        $progressPercentage = $user->progress_percentage ?? null;

        if (is_null($nextLevel) || is_null($pointsToNextLevel) || is_null($progressPercentage)) {
            $next = DB::table('user_levels')
                ->where('min_points', '>', $totalPoints)
                ->orderBy('min_points')
                ->first();
            $nextLevel = $next->level ?? null;
            $pointsToNextLevel = $next ? max(0, $next->min_points - $totalPoints) : 0;
            $progressPercentage = $next
                ? (int) round(($totalPoints / max(1, $next->min_points)) * 100)
                : 100;
        }

        $rewards = RewardStore::with(['category','commerce'])
            ->where('stock', '>', 0)
            ->when($request->filled('category'), fn($q) => $q->where('reward_category_id', $request->category))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->orderByDesc('created_at')
            ->get();

        $maxRewardCost     = (int) RewardStore::max('points_required');
        $redemptionsCount  = UserReward::where('user_id', $user->id)->count();
        $donationsCount    = DB::table('donations')->where('donor_id', $user->id)->count();
        $rankPosition      = null;
        $badges            = [];
        $ownedCoupons      = [];
        $activities        = [];
        $leaderboard       = [];

        return view('donor.store', compact(
            'user', 'totalPoints', 'currentLevel', 'nextLevel',
            'pointsToNextLevel', 'progressPercentage', 'rewards', 'maxRewardCost',
            'redemptionsCount', 'donationsCount', 'rankPosition', 'badges', 'ownedCoupons',
            'activities', 'leaderboard'
        ));
    }

    public function redeem(Request $request, RewardStore $reward)
    {
        $user = $request->user();

        $preTotalPoints = (int) UserPoint::where('user_id', $user->id)->sum('points');
        if ($preTotalPoints < (int) $reward->points_required) {
            return $this->respondError($request, 'No tienes puntos suficientes para esta recompensa.');
        }
        if ((int) $reward->stock <= 0) {
            return $this->respondError($request, 'No hay stock disponible.');
        }

        try {
            $userReward = null;

            DB::transaction(function () use ($user, $reward, &$userReward) {
                DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
                $lockedReward = RewardStore::where('id', $reward->id)->lockForUpdate()->firstOrFail();

                if ((int) $lockedReward->stock <= 0) {
                    throw new \RuntimeException('Stock agotado durante el canje.');
                }

                $availablePoints = (int) UserPoint::where('user_id', $user->id)->sum('points');
                if ($availablePoints < (int) $lockedReward->points_required) {
                    throw new \RuntimeException('No tienes puntos suficientes para esta recompensa.');
                }

                $userReward = UserReward::create([
                    'id'          => (string) Str::uuid(),
                    'user_id'     => $user->id,
                    'reward_id'   => $lockedReward->id,
                    'status'      => 'delivered', // puedes cambiar a "redeemed" si quieres un estado intermedio
                    'redeemed_at' => now(),
                ]);

                UserPoint::create([
                    'id'          => (string) Str::uuid(),
                    'user_id'     => $user->id,
                    'action'      => 'reward_redeemed',
                    'points'      => -1 * (int) $lockedReward->points_required,
                    'description' => 'Canje de recompensa: '.$lockedReward->name,
                    'created_at'  => now(),
                ]);

                $affected = RewardStore::where('id', $lockedReward->id)
                    ->where('stock', '>=', 1)
                    ->decrement('stock', 1);

                if ($affected === 0) {
                    throw new \RuntimeException('Stock agotado durante el canje.');
                }

                if ((int) $lockedReward->stock === 1) {
                    $lockedReward->delete();
                }

                // ✉️ Enviar correos luego del commit
                DB::afterCommit(function () use ($user, $lockedReward, $userReward) {
                    $companyEmail = env('COMPANY_EMAIL', config('mail.from.address'));

                    // correo al donador
                    Mail::to($user->email)->send(new RewardRedeemedMail($user, $lockedReward, $userReward));

                    // correo a la empresa
                    Mail::to($companyEmail)->send(new RewardRedeemedAdminMail($user, $lockedReward, $userReward));
                });
            });

            $freshPoints = (int) UserPoint::where('user_id', $user->id)->sum('points');

            return $this->respondOk(
                $request,
                '¡Canje realizado con éxito!',
                [
                    'redemption_id' => $userReward?->id,
                    'points'        => $freshPoints,
                    'reward'        => [
                        'id'    => $reward->id,
                        'name'  => $reward->name,
                        'cost'  => (int) $reward->points_required,
                    ],
                ]
            );

        } catch (\Throwable $e) {
            $msg = $e instanceof \RuntimeException ? $e->getMessage() : 'No se pudo completar el canje.';
            return $this->respondError($request, $msg);
        }
    }

    protected function respondOk(Request $request, string $message, array $extra = [])
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message] + $extra);
        }
        return back()->with('ok', $message);
    }

    protected function respondError(Request $request, string $message, int $status = 422)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $message], $status);
        }
        return back()->withErrors($message);
    }
}