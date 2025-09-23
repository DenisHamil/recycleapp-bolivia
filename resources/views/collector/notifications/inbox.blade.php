@extends('collector.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📬 Bandeja de Notificaciones</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        use Illuminate\Support\Facades\DB;
    @endphp

    @if ($notifications->isEmpty())
        <div class="alert alert-info">No tienes notificaciones por ahora.</div>
    @else
        <ul class="list-group">
            @foreach ($notifications as $notification)
                @php
                    // Ocultar 'rating.request' si ya fue calificado
                    $ocultar = false;

                    if ($notification->type === 'rating.request' && $notification->related_id) {
                        $ocultar = DB::table('ratings')
                            ->where('donation_id', $notification->related_id)
                            ->where('from_user_id', auth()->id())
                            ->exists();
                    }
                @endphp

                @if (!$ocultar)
                    <li class="list-group-item d-flex justify-content-between align-items-start {{ !$notification->is_read ? 'list-group-item-light border-primary' : '' }}">
                        <div class="w-100 position-relative">
                            <h5 class="mb-1">
                                {{ $notification->title }}

                                @if (!$notification->is_read)
                                    <span class="badge bg-danger rounded-circle position-absolute top-0 end-0" style="width: 10px; height: 10px;">
                                        <span class="visually-hidden">No leída</span>
                                    </span>
                                @endif
                            </h5>

                            <small class="text-muted d-block mb-1">
                                Tipo: {{ ucfirst($notification->type) }} |
                                @if ($notification->created_at)
                                    <span class="notification-time"
                                          data-created-at="{{ $notification->created_at->toIso8601String() }}">
                                        {{ $notification->created_at->format('Y-m-d H:i:s') }}
                                    </span>
                                @else
                                    <em>Fecha no disponible</em>
                                @endif
                            </small>

                            <p class="mb-2">{{ \Illuminate\Support\Str::limit($notification->message, 100) }}</p>

                            <a href="{{ route('collector.notifications.show', $notification->id) }}" class="btn btn-sm btn-primary">
                                Ver
                            </a>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Convertir las fechas al formato local del navegador
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.notification-time').forEach(el => {
            const iso = el.getAttribute('data-created-at');
            if (!iso) return;
            const date = new Date(iso);
            if (!isNaN(date)) {
                el.textContent = date.toLocaleString('es-BO', {
                    dateStyle: 'short',
                    timeStyle: 'short'
                });
            }
        });
    });
</script>
@endsection
