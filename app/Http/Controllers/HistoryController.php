<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    use App\Models\ActivityLog;
    use App\Models\Donation;
    use App\Models\Notification;

    class HistoryController extends Controller
    {
        public function index(Request $request)
        {
            $context = $request->routeIs('collector.*') ? 'collector' : 'donor';
            if (!Auth::check() || Auth::user()->role !== $context) {
                return redirect()->route('login');
            }

            $userId = Auth::id();
            $filters = [
                'type'   => trim((string) $request->get('type', '')),
                'entity' => trim((string) $request->get('entity', '')),
                'q'      => trim((string) $request->get('q', '')),
                'from'   => $request->get('from'),
                'to'     => $request->get('to'),
            ];

            $allowedSizes = [10, 15, 25, 50];
            $perPageReq = (int) $request->get('per_page', 15);
            $perPage = in_array($perPageReq, $allowedSizes, true) ? $perPageReq : 15;

            $allLogs = ActivityLog::query()
                ->where('user_id', $userId)
                ->when($filters['type'], fn($q) => $q->where('action_type', $filters['type']))
                ->when($filters['entity'], fn($q) => $q->where('reference_table', $filters['entity']))
                ->when($filters['q'], fn($q) => $q->where('detail', 'like', '%' . $filters['q'] . '%'))
                ->when($filters['from'], fn($q) => $q->where('created_at', '>=', Carbon::parse($filters['from'])->startOfDay()))
                ->when($filters['to'], fn($q) => $q->where('created_at', '<=', Carbon::parse($filters['to'])->endOfDay()))
                ->where('reference_table', 'donations')
                ->orderByDesc('created_at')
                ->get();

            $logs = $allLogs->groupBy('reference_id')->map(function ($group) {
                return $group->sortByDesc('created_at')->first();
            })->values();

            $page = request('page', 1);
            $logs = $logs->forPage($page, $perPage);
            $paginatedLogs = new \Illuminate\Pagination\LengthAwarePaginator(
                $logs,
                $allLogs->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            $donationIds = $logs->pluck('reference_id')->unique();
            $donationsQ = Donation::with('category');
            if ($context === 'donor') {
                $donationsQ->where('donor_id', $userId);
            } else {
                $donationsQ->where('collector_id', $userId);
            }
            $donationsById = $donationsQ->whereIn('id', $donationIds)->get()->keyBy('id');

            $now = Carbon::now();
            $todayFrom = $now->copy()->startOfDay();
            $weekFrom = $now->copy()->startOfWeek();

            $stats = [
                'total' => $allLogs->count(),
                'today' => $allLogs->where('created_at', '>=', $todayFrom)->count(),
                'this_week' => $allLogs->where('created_at', '>=', $weekFrom)->count(),
            ];

            $typeCounts = $allLogs->groupBy('action_type')->map(fn($g) => ['action_type' => $g->first()->action_type, 'c' => $g->count()])->values();

            $view = $context === 'collector'
                ? 'collector.history.index'
                : 'donor.history.index';

            return view($view, [
                'logs' => $paginatedLogs,
                'filters' => $filters,
                'stats' => $stats,
                'typeCounts' => $typeCounts,
                'perPage' => $perPage,
                'donationsById' => $donationsById,
            ]);
        }

        public function donorCancel(Request $request, string $donationId)
        {
            $user = $request->user();
            if (!$user || $user->role !== 'donor') {
                return redirect()->route('login');
            }

            $data = $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            $donation = Donation::with(['collector'])->where('id', $donationId)->firstOrFail();

            if ((string) $donation->donor_id !== (string) $user->id) {
                abort(403, 'No estás autorizado para cancelar esta donación.');
            }

            if (empty($donation->collector_id)) {
                return back()->with('error', 'Aún no hay un recolector asignado. No es necesario cancelar.');
            }

            if ($donation->state !== 'accepted') {
                return back()->with('error', 'Solo puedes cancelar donaciones aceptadas y aún no completadas.');
            }

            DB::transaction(function () use ($donation, $data, $user) {
                $oldCollectorId = $donation->collector_id;

                $donation->state = 'available';
                $donation->cancel_reason = $data['reason'];
                $donation->collector_id = null;
                $donation->confirmed_by_collector = false;
                $donation->save();

                Notification::insert([
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $oldCollectorId,
                        'title' => '❌ Donación cancelada por el donador',
                        'message' => 'Motivo: ' . $donation->cancel_reason,
                        'type' => 'donation.cancelled',
                        'related_id' => $donation->id,
                        'is_read' => false,
                        'created_at' => now(),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $user->id,
                        'title' => '⭐ Califica al recolector',
                        'message' => 'La donación fue cancelada. Puedes dejar una calificación al recolector.',
                        'type' => 'rating.request',
                        'related_id' => $donation->id,
                        'is_read' => false,
                        'created_at' => now(),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $oldCollectorId,
                        'title' => '⭐ Califica al donador',
                        'message' => 'La donación fue cancelada. Puedes dejar una calificación al donador.',
                        'type' => 'rating.request',
                        'related_id' => $donation->id,
                        'is_read' => false,
                        'created_at' => now(),
                    ]
                ]);

                DB::table('activity_log')->insert([
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $user->id,
                        'action_type' => 'donation.cancelled',
                        'reference_table' => 'donations',
                        'reference_id' => $donation->id,
                        'detail' => $donation->cancel_reason,
                        'created_at' => now(),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $oldCollectorId,
                        'action_type' => 'collection.cancelled',
                        'reference_table' => 'donations',
                        'reference_id' => $donation->id,
                        'detail' => 'Cancelada por el donador: ' . $donation->cancel_reason,
                        'created_at' => now(),
                    ]
                ]);
            });

            return back()->with('success', 'Donación cancelada correctamente.');
        }

        public function collectorCancel(Request $request, string $donationId)
        {
            $user = $request->user();
            if (!$user || $user->role !== 'collector') {
                return redirect()->route('login');
            }

            $data = $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            $donation = Donation::with(['donor'])->where('id', $donationId)->firstOrFail();

            if ((string) $donation->collector_id !== (string) $user->id) {
                abort(403, 'No estás autorizado para cancelar esta recolección.');
            }

            if ($donation->state !== 'accepted') {
                return back()->with('error', 'Solo puedes cancelar recolecciones aceptadas y aún no completadas.');
            }

            DB::transaction(function () use ($donation, $data, $user) {
                $oldDonorId = $donation->donor_id;

                $donation->state = 'available';
                $donation->cancel_reason = $data['reason'];
                $donation->collector_id = null;
                $donation->confirmed_by_collector = false;
                $donation->save();

                Notification::insert([
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $oldDonorId,
                        'title' => '❌ Recolección cancelada por el recolector',
                        'message' => 'Motivo: ' . $donation->cancel_reason,
                        'type' => 'collection.cancelled',
                        'related_id' => $donation->id,
                        'is_read' => false,
                        'created_at' => now(),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $oldDonorId,
                        'title' => '⭐ Califica al recolector',
                        'message' => 'La recolección fue cancelada. Puedes dejar una calificación al recolector.',
                        'type' => 'rating.request',
                        'related_id' => $donation->id,
                        'is_read' => false,
                        'created_at' => now(),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $user->id,
                        'title' => '⭐ Califica al donador',
                        'message' => 'La recolección fue cancelada. Puedes dejar una calificación al donador.',
                        'type' => 'rating.request',
                        'related_id' => $donation->id,
                        'is_read' => false,
                        'created_at' => now(),
                    ]
                ]);

                DB::table('activity_log')->insert([
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $user->id,
                        'action_type' => 'collection.cancelled',
                        'reference_table' => 'donations',
                        'reference_id' => $donation->id,
                        'detail' => $donation->cancel_reason,
                        'created_at' => now(),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'user_id' => $oldDonorId,
                        'action_type' => 'donation.cancelled',
                        'reference_table' => 'donations',
                        'reference_id' => $donation->id,
                        'detail' => 'Cancelada por el recolector: ' . $donation->cancel_reason,
                        'created_at' => now(),
                    ]
                ]);
            });

            return back()->with('success', 'Recolección cancelada correctamente.');
        }
    }
