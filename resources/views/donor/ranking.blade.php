@extends('donor.layouts.app')

@section('content')
@php
  $perPage = $perPage ?? 10;
  $sizes   = [10,15,25,50];
@endphp

<div class="container py-4">
  <h1 class="mb-4 fw-bold text-success">🏆 Ranking</h1>

  <form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-sm-6 col-md-4">
      <label class="form-label">Resultados por página</label>
      <select class="form-select" name="per_page">
        @foreach($sizes as $s)
          <option value="{{ $s }}" @selected((int)request('per_page', $perPage)===$s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button class="btn btn-success flex-grow-1" type="submit">Aplicar</button>
      <a class="btn btn-outline-secondary" href="{{ route('donor.ranking') }}">Limpiar</a>
    </div>
  </form>

  <div class="row g-4">
    {{-- Top Donadores --}}
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-success text-white">
          <strong>🌿 Top Donadores (finalizadas)</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:56px;"></th>
                  <th>Usuario</th>
                  <th class="text-center">🌟 Promedio</th>
                  <th class="text-center"># Reseñas</th>
                </tr>
              </thead>
              <tbody>
                @forelse($donorsLeaderboard as $row)
                  @php
                    $fullName = trim(($row->first_name ?? '').' '.($row->last_name ?? ''));
                    $imgPath  = $row->profile_image_path
                      ? asset('storage/'.ltrim($row->profile_image_path, '/'))
                      : null;
                    $initials = strtoupper(mb_substr($row->first_name ?? 'U', 0, 1).mb_substr($row->last_name ?? '', 0, 1));
                  @endphp
                  <tr>
                    <td>
                      @if($imgPath)
                        <img src="{{ $imgPath }}" alt="avatar" class="rounded-circle" width="40" height="40" style="object-fit:cover;">
                      @else
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                             style="width:40px;height:40px;">{{ $initials }}</div>
                      @endif
                    </td>
                    <td>{{ $fullName ?: 'Usuario' }}</td>
                    <td class="text-center">
                      <span class="badge bg-light text-success border border-success">
                        <i class="bi bi-star-fill me-1"></i>{{ number_format($row->avg_stars, 2) }}
                      </span>
                    </td>
                    <td class="text-center">{{ $row->reviews_count }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted p-3">No hay datos para mostrar.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        @if($donorsLeaderboard->hasPages())
          <div class="card-footer">
            {{ $donorsLeaderboard->appends(['per_page' => $perPage])->links() }}
          </div>
        @endif
      </div>
    </div>

    {{-- Top Recolectores --}}
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-warning">
          <strong>🚚 Top Recolectores (finalizadas)</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:56px;"></th>
                  <th>Usuario</th>
                  <th class="text-center">🌟 Promedio</th>
                  <th class="text-center"># Reseñas</th>
                </tr>
              </thead>
              <tbody>
                @forelse($collectorsLeaderboard as $row)
                  @php
                    $fullName = trim(($row->first_name ?? '').' '.($row->last_name ?? ''));
                    $imgPath  = $row->profile_image_path
                      ? asset('storage/'.ltrim($row->profile_image_path, '/'))
                      : null;
                    $initials = strtoupper(mb_substr($row->first_name ?? 'U', 0, 1).mb_substr($row->last_name ?? '', 0, 1));
                  @endphp
                  <tr>
                    <td>
                      @if($imgPath)
                        <img src="{{ $imgPath }}" alt="avatar" class="rounded-circle" width="40" height="40" style="object-fit:cover;">
                      @else
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center"
                             style="width:40px;height:40px;">{{ $initials }}</div>
                      @endif
                    </td>
                    <td>{{ $fullName ?: 'Usuario' }}</td>
                    <td class="text-center">
                      <span class="badge bg-light text-warning border border-warning">
                        <i class="bi bi-star-fill me-1"></i>{{ number_format($row->avg_stars, 2) }}
                      </span>
                    </td>
                    <td class="text-center">{{ $row->reviews_count }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted p-3">No hay datos para mostrar.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        @if($collectorsLeaderboard->hasPages())
          <div class="card-footer">
            {{ $collectorsLeaderboard->appends(['per_page' => $perPage])->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
