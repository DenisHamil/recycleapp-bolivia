@extends('collector.layouts.app')
@section('title','Historial')
@section('content')

<h1 class="h4 mb-4">📜 Historial</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Material</th>
                    <th>Estado</th>
                    <th>Detalle</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    @php
                        $donation = $donationsById[$log->reference_id] ?? null;
                        $category = $donation?->category?->name ?? 'Desconocido';
                        $state = $donation?->state ?? '—';
                        $stateLabels = [
                            'pending' => 'Pendiente',
                            'open' => 'Abierta',
                            'available' => 'Disponible',
                            'accepted' => 'Accepted',
                            'completed' => 'Completada',
                            'cancelled' => 'Cancelled',
                        ];
                        $badgeColors = [
                            'pending' => 'secondary',
                            'open' => 'info',
                            'available' => 'info',
                            'accepted' => 'warning',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                        ];
                        $canCancel = $donation && $donation->state === 'accepted';
                    @endphp
                    <tr>
                        <td>
                            <span class="badge bg-secondary">{{ $category }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $badgeColors[$state] ?? 'light' }}">
                                {{ $stateLabels[$state] ?? ucfirst($state) }}
                            </span>
                        </td>
                        <td>{{ $log->detail ?? '—' }}</td>
                        <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            @if ($canCancel)
                                <form method="POST" action="{{ route('collector.history.cancel', $donation->id) }}">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('¿Estás seguro de cancelar esta recolección?')">
                                        Cancelar
                                    </button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Sin movimientos recientes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $logs->withQueryString()->links() }}
    </div>
</div>

@endsection
