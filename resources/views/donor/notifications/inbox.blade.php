@extends('donor.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📨 Bandeja de Entrada</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        use Illuminate\Pagination\AbstractPaginator;

        // Normalizamos items y los ordenamos por created_at DESC independientemente del tipo
        $items = $notifications instanceof AbstractPaginator
            ? collect($notifications->items())
            : collect($notifications);

        $items = $items->sortByDesc(fn($n) => $n->created_at);
    @endphp

    @if ($items->isEmpty())
        <div class="alert alert-info">No tienes notificaciones por ahora.</div>
    @else
        @foreach ($items as $n)
            <div class="card mb-3 {{ $n->is_read ? '' : 'border-primary' }}">
                <div class="card-body position-relative">
                    <h5 class="card-title mb-1">
                        {{ $n->title }}
                        @if (!$n->is_read)
                            <span class="badge bg-danger rounded-circle position-absolute top-0 end-0 m-2"
                                  style="width:10px;height:10px;">
                                <span class="visually-hidden">No leída</span>
                            </span>
                        @endif
                    </h5>

                    <small class="text-muted d-block mb-1">
                        Tipo: {{ ucfirst($n->type) }} |
                        Recibido:
                        @if ($n->created_at)
                            <span class="notification-time"
                                  data-created-at="{{ $n->created_at }}">
                                {{ $n->created_at }}
                            </span>
                        @else
                            <em>Fecha no disponible</em>
                        @endif
                    </small>

                    <p class="card-text text-muted mb-2">
                        {{ \Illuminate\Support\Str::limit($n->message, 120) }}
                    </p>

                    <a href="{{ route('donor.notifications.show', $n->id) }}"
                       class="btn btn-sm btn-outline-primary">
                        Ver
                    </a>
                </div>
            </div>
        @endforeach

        {{-- Paginación si el controlador usa paginate() --}}
        @if ($notifications instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Convierte la fecha del servidor a hora local del navegador (ej: es-BO)
    document.querySelectorAll('.notification-time').forEach(el => {
        const iso = el.getAttribute('data-created-at');
        if (!iso) return;
        const d = new Date(iso);
        if (isNaN(d.getTime())) return;
        el.textContent = d.toLocaleString('es-BO', {
            dateStyle: 'short',
            timeStyle: 'short'
        });
    });
});
</script>
@endsection
