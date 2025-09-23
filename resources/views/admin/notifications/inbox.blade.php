@extends('admin.layout')

@section('title', 'Bandeja de notificaciones')

@section('content')
@php
    use Illuminate\Support\Str;

    $statuses = ['new' => 'Nueva', 'processing' => 'En proceso', 'done' => 'Resuelta', 'error' => 'Error'];
    $priorities = [1 => '1 (Alta)', 2 => '2', 3 => '3 (Media)', 4 => '4', 5 => '5 (Baja)'];

    // ✅ Mostrar todas las notificaciones de canjes exitosos para admin
    $filteredNotifications = $notifications->filter(function ($n) {
        return $n->related_type === 'reward_redemptions'
            && $n->type === 'reward_redemption.fulfilled';
    })->sortByDesc('created_at');

    $unreadCount = $filteredNotifications->where('is_read', false)->count();
@endphp

<style>
    .card { background:#fff; border-radius:16px; padding:16px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
    .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; line-height:1; }
    .badge-gray { background:#f1f5f9; color:#0f172a; }
    .badge-green { background:#dcfce7; color:#14532d; }
    .badge-amber { background:#fef3c7; color:#7c2d12; }
    .badge-red { background:#fee2e2; color:#7f1d1d; }
    .badge-blue { background:#dbeafe; color:#1e3a8a; }
    .btn { display:inline-flex; align-items:center; gap:.25rem; padding:.5rem .75rem; border-radius:10px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; text-decoration:none; }
    .btn:hover { background:#f9fafb; }
    .btn-primary { border-color:#2563eb; }
    .btn-danger { border-color:#dc2626; }
    .btn-muted { color:#475569; }
    .table { width:100%; border-collapse:separate; border-spacing:0; }
    .table th, .table td { padding:12px 10px; border-bottom:1px solid #f1f5f9; vertical-align:top; }
    .table th { text-align:left; font-size:.85rem; color:#475569; font-weight:600; }
    .table tr.unread td { background:#f8fafc; }
    .dot { width:8px; height:8px; border-radius:999px; display:inline-block; margin-right:6px; background:#22c55e; }
</style>

<div class="card" style="margin-bottom:16px;">
    <h1 style="font-size:1.25rem; margin:0;">📬 Bandeja de notificaciones</h1>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <span class="badge badge-blue">Total: {{ number_format($filteredNotifications->count()) }}</span>
            <span class="badge badge-amber" title="No leídas">No leídas: {{ number_format($unreadCount) }}</span>
        </div>
        <div style="display:flex; gap:8px;">
            @if($unreadCount)
                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-muted" type="submit">Marcar todas como leídas</button>
                </form>
            @endif
        </div>
    </div>

    <div style="overflow:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:28px;">&nbsp;</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Fecha</th>
                    <th style="width:260px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filteredNotifications as $n)
                    @php
                        $status = $n->status ?? null;
                        $priority = (int) ($n->priority ?? 3);
                        $badgeStatusClass = match($status){
                            'new' => 'badge-blue', 'processing' => 'badge-amber', 'done' => 'badge-green', 'error' => 'badge-red', default => 'badge-gray'
                        };
                        $badgePriorityClass = $priority <= 2 ? 'badge-red' : ($priority === 3 ? 'badge-amber' : 'badge-gray');
                        $payload = is_array($n->payload) ? $n->payload : [];
                        $subtitle = data_get($payload, 'reward_name');
                    @endphp
                    <tr class="{{ $n->is_read ? '' : 'unread' }}">
                        <td>@if(!$n->is_read)<span class="dot" title="Sin leer"></span>@endif</td>
                        <td>
                            <div style="font-weight:600;">{{ $n->title ?? '(Sin título)' }}</div>
                            <div style="color:#475569; font-size:.9rem;">{{ Str::limit($n->message ?? '', 120) }}</div>
                            @if($subtitle)
                                <div style="color:#64748b; font-size:.8rem; margin-top:2px;">🎁 {{ $subtitle }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-gray">{{ $n->type ?? '—' }}</span></td>
                        <td><span class="badge {{ $badgeStatusClass }}">{{ $statuses[$status] ?? ($status ?: '—') }}</span></td>
                        <td><span class="badge {{ $badgePriorityClass }}">{{ $priority }}</span></td>
                        <td>
                            <div>{{ optional($n->created_at)->format('Y-m-d H:i') ?? '—' }}</div>
                            @if(!empty($n->read_at))
                                <div style="color:#64748b; font-size:.8rem;">Visto: {{ \Carbon\Carbon::parse($n->read_at)->format('Y-m-d H:i') }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                @php $cta = $n->action_url; @endphp
                                @if($cta)
                                    <a class="btn btn-primary" href="{{ $cta }}">Ver</a>
                                @else
                                    <a class="btn" href="{{ route('admin.notifications.show', $n->id) }}">Ver</a>
                                @endif

                                @if(!$n->is_read)
                                    <form method="POST" action="{{ route('admin.notifications.mark-as-read', $n->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn" type="submit">Marcar leída</button>
                                    </form>
                                @endif

                                @if(($n->status ?? null) !== 'processing' && ($n->status ?? null) !== 'done')
                                    <form method="POST" action="{{ route('admin.notifications.update-status', $n->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button class="btn" type="submit">Tomar</button>
                                    </form>
                                @endif

                                @if(($n->status ?? null) !== 'done')
                                    <form method="POST" action="{{ route('admin.notifications.update-status', $n->id) }}" onsubmit="return confirm('¿Marcar como resuelta?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button class="btn" type="submit">Resolver</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:#64748b; padding:20px;">No hay notificaciones de canjes exitosos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
