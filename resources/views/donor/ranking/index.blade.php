{{-- resources/views/ranking/index.blade.php --}}
@extends('donor.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0"><i class="bi bi-trophy-fill me-2"></i>Ranking</h1>

        <form class="d-flex gap-2" method="GET" action="{{ route('ranking') }}">
            <div class="input-group" style="max-width: 180px;">
                <span class="input-group-text"><i class="bi bi-funnel-fill"></i></span>
                <input type="number" min="1" class="form-control" name="min_reviews" value="{{ $minReviews }}" placeholder="Mín. reseñas">
            </div>
            <div class="input-group" style="max-width: 180px;">
                <span class="input-group-text"><i class="bi bi-list-ol"></i></span>
                <input type="number" min="5" class="form-control" name="per_page" value="{{ $perPage }}" placeholder="Por página">
            </div>
            <button class="btn btn-success" type="submit">
                <i class="bi bi-arrow-repeat me-1"></i>Aplicar
            </button>
        </form>
    </div>

    <div class="row g-4">
        {{-- Tabla Donadores --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white d-flex align-items-center">
                    <i class="bi bi-people-fill me-2"></i> Top Donadores (solo finalizados)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th class="text-center">Promedio</th>
                                    <th class="text-center">Reseñas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($donorsLeaderboard as $idx => $row)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ ($donorsLeaderboard->currentPage()-1)*$donorsLeaderboard->perPage() + $idx + 1 }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $row->profile_image_path ? asset($row->profile_image_path) : asset('images/default-profile.png') }}"
                                                     alt="avatar" class="rounded-circle me-2 border" width="40" height="40">
                                                <div>
                                                    <div class="fw-semibold">{{ $row->first_name }} {{ $row->last_name }}</div>
                                                    <div class="text-muted small">ID: {{ $row->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success border">
                                                <i class="bi bi-star-fill me-1"></i>{{ number_format($row->avg_stars, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $row->reviews_count }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted p-4">Sin datos suficientes</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($donorsLeaderboard->hasPages())
                    <div class="card-footer">
                        {{ $donorsLeaderboard->appends(['min_reviews' => $minReviews, 'per_page' => $perPage])->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabla Recolectores --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white d-flex align-items-center">
                    <i class="bi bi-truck-front-fill me-2"></i> Top Recolectores (solo finalizados)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th class="text-center">Promedio</th>
                                    <th class="text-center">Reseñas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectorsLeaderboard as $idx => $row)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ ($collectorsLeaderboard->currentPage()-1)*$collectorsLeaderboard->perPage() + $idx + 1 }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $row->profile_image_path ? asset($row->profile_image_path) : asset('images/default-profile.png') }}"
                                                     alt="avatar" class="rounded-circle me-2 border" width="40" height="40">
                                                <div>
                                                    <div class="fw-semibold">{{ $row->first_name }} {{ $row->last_name }}</div>
                                                    <div class="text-muted small">ID: {{ $row->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success border">
                                                <i class="bi bi-star-fill me-1"></i>{{ number_format($row->avg_stars, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $row->reviews_count }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted p-4">Sin datos suficientes</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($collectorsLeaderboard->hasPages())
                    <div class="card-footer">
                        {{ $collectorsLeaderboard->appends(['min_reviews' => $minReviews, 'per_page' => $perPage])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
