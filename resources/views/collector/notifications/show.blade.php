@extends('collector.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0 bg-light">
        <div class="card-body">
            <h4 class="mb-3">
                <i class="bi bi-envelope-arrow-down text-primary"></i>
                {{ $notification->title }}
            </h4>

            <p class="text-muted mb-1">
                Tipo: {{ ucfirst($notification->type) }} |
                Recibido:
                {{ $notification->created_at ? $notification->created_at->format('d/m/Y H:i') : 'Fecha no disponible' }}
            </p>

            <div class="alert alert-info mt-3">
                {{ $notification->message }}
            </div>

            {{-- Mensaje de éxito y redirección automática si se acaba de calificar --}}
            @if(session('success'))
                <div class="alert alert-success mt-4">
                    {{ session('success') }}
                </div>

                <script>
                    setTimeout(() => {
                        window.location.href = "{{ route('collector.notifications.index') }}";
                    }, 2000); // redirige luego de 2 segundos
                </script>
            @endif

            @php
                use Illuminate\Support\Facades\DB;
                $alreadyRated = DB::table('ratings')
                    ->where('donation_id', $proposal->donation->id ?? null)
                    ->where('from_user_id', auth()->id())
                    ->exists();
            @endphp

            @if ($notification->type === 'rating.request' && $proposal && !$alreadyRated)
                <form action="{{ route('collector.ratings.store') }}" method="POST" class="mt-4 rating-form">
                    @csrf

                    <input type="hidden" name="to_user_id" value="{{ $proposal->donation->donor->id }}">
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
            @elseif($alreadyRated)
                <div class="alert alert-secondary mt-4">
                    Ya calificaste esta donación. ¡Gracias por tu aporte!
                </div>
            @elseif($proposal)
                <div class="mt-4">
                    <h5>Detalles de la propuesta asignada:</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><strong>Residuo:</strong> {{ $proposal->donation->category->name ?? 'Sin categoría' }}</li>
                        <li class="list-group-item"><strong>Descripción:</strong> {{ $proposal->donation->description ?? 'No especificada' }}</li>
                        <li class="list-group-item"><strong>Dirección:</strong> {{ $proposal->donation->address_description ?? 'No disponible' }}</li>
                        <li class="list-group-item"><strong>Fecha propuesta:</strong>
                            {{ $proposal->proposed_date ?? 'No definida' }} {{ $proposal->proposed_time ?? 'No definida' }}
                        </li>
                    </ul>
                </div>
            @else
                <p class="text-muted mt-3">No hay información adicional de esta propuesta.</p>
            @endif

            <div class="mt-4">
                <a href="{{ route('collector.notifications.index') }}" class="btn btn-secondary">
                    Volver a la bandeja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/block-buttons.js') }}"></script>
@endsection
