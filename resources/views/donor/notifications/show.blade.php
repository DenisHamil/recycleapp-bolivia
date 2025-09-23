@extends('donor.layouts.app')

@section('content')
<div class="container">
    <h2>{{ $notification->title }}</h2>
    <p class="text-muted">
        Tipo: {{ ucfirst(str_replace('.', ' ', $notification->type)) }} |
        Recibido:
        @if ($notification->created_at)
            <span class="notification-time" data-created-at="{{ $notification->created_at }}">
                {{ $notification->created_at->format('d/m/Y H:i') }}
            </span>
        @else
            Fecha no disponible
        @endif
    </p>

    <div class="card p-3 mb-4">
        {{ $notification->message }}
    </div>

    {{-- Formulario para calificar al recolector solo si es tipo rating --}}
    @if ($notification->type === 'rating.request' && $proposal)
        <form action="{{ route('donor.ratings.store') }}" method="POST" class="mb-4 rating-form">
            @csrf

            <input type="hidden" name="to_user_id" value="{{ $proposal->collector->id }}">
            <input type="hidden" name="donation_id" value="{{ $proposal->donation->id }}">

            <div class="mb-3">
                <label for="stars" class="form-label">Calificación:</label>
                <select name="stars" id="stars" class="form-select" required>
                    <option value="">Selecciona una puntuación</option>
                    <option value="5">⭐⭐⭐⭐⭐ (Excelente)</option>
                    <option value="4">⭐⭐⭐⭐ (Muy bueno)</option>
                    <option value="3">⭐⭐⭐ (Bueno)</option>
                    <option value="2">⭐⭐ (Regular)</option>
                    <option value="1">⭐ (Malo)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="comment" class="form-label">Comentario (opcional):</label>
                <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-success submit-rating-btn">Enviar calificación</button>
        </form>
    @elseif ($proposal)
        {{-- Solo mostrar detalles de la propuesta si NO es tipo rating --}}
        <div class="card p-4 mb-4 bg-light border-success">
            <h5 class="mb-3">📦 Detalles de la Propuesta</h5>

            <p><strong>Empresa recolectora:</strong> {{ $proposal->collector->company_name ?? 'Sin nombre' }}</p>
            <p><strong>Email del recolector:</strong> {{ $proposal->collector->email ?? 'N/A' }}</p>

            <p><strong>Fecha propuesta:</strong>
                {{ $proposal->proposed_date ? \Carbon\Carbon::parse($proposal->proposed_date)->format('d/m/Y') : 'No definida' }}
                a las
                {{ $proposal->proposed_time ? \Carbon\Carbon::parse($proposal->proposed_time)->format('H:i') : 'No definida' }}
            </p>

            <p><strong>Dirección de la donación:</strong> {{ $proposal->donation->address ?? 'No definida' }}</p>
            <p><strong>Categoría del residuo:</strong> {{ $proposal->donation->category->name ?? 'No disponible' }}</p>
            <p><strong>Peso estimado:</strong> {{ $proposal->donation->estimated_weight ?? 'No definido' }}</p>
            <p><strong>Descripción:</strong> {{ $proposal->donation->description ?? 'No hay descripción' }}</p>

            @if ($proposal->donation->image_path)
                <p><strong>Imagen:</strong></p>
                <img src="{{ asset('residuos/' . $proposal->donation->image_path) }}"
                     alt="Imagen del residuo"
                     class="img-fluid rounded"
                     style="max-width: 300px;">
            @endif

            <form action="{{ route('donor.proposals.accept', $proposal->id) }}" method="POST" onsubmit="handleProposalSubmit(this)">
                @csrf
                <button type="submit" class="btn btn-success mt-3" id="accept-btn">
                    <i class="bi bi-check-circle-fill me-1"></i> Aceptar propuesta
                </button>
            </form>
        </div>
    @elseif ($relatedProposalMissing)
        <div class="alert alert-danger">
            Esta notificación hacía referencia a una propuesta que ya no está disponible.
        </div>
    @else
        <div class="alert alert-warning">
            Esta notificación no contiene detalles válidos de una propuesta.
        </div>
    @endif

    <a href="{{ route('donor.notifications.index') }}" class="btn btn-secondary mt-4">← Volver a la bandeja</a>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/block-buttons.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.notification-time').forEach(el => {
            const utcDate = el.dataset.createdAt;
            if (utcDate) {
                const localDate = new Date(utcDate);
                el.textContent = localDate.toLocaleString('es-BO', {
                    dateStyle: 'short',
                    timeStyle: 'short'
                });
            }
        });
    });
</script>
@endsection
