@extends('collector.layouts.app')

@section('content')
@php
    use App\Models\Category;
    $categories = Category::all();
@endphp

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="{{ asset('css/map-style.css') }}">

<h2 class="mb-4 fw-bold text-primary text-center">📍 Mapa de Donaciones Disponibles</h2>

<!-- Input oculto para pasar el ID del recolector autenticado a JS -->
<input type="hidden" id="authUserId" value="{{ Auth::id() }}">

<div class="row">
    <div class="col-md-9">
        <div id="map" style="height: 600px;" class="rounded border shadow-sm"></div>
    </div>
    <div class="col-md-3">
        <h5 class="fw-bold mb-3">🗂️ Leyenda de Categorías</h5>
        <ul class="list-group small">
            @foreach ($categories as $category)
                <li class="list-group-item d-flex align-items-center">
                    <span class="me-2 rounded-circle" style="width: 20px; height: 20px; background-color: {{ $category->color }};"></span>
                    {{ $category->name }}
                </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- MODAL: Enviar propuesta --}}
<div class="modal fade" id="proposalModal" tabindex="-1" aria-labelledby="proposalModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="proposalForm" method="POST" action="{{ route('collector.proposals.store') }}">
      @csrf
      <input type="hidden" name="donation_id" id="donationIdInput">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="proposalModalLabel">Enviar propuesta de recolección</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="proposed_date" class="form-label">Fecha propuesta</label>
            <input type="date" class="form-control" name="proposed_date" required>
          </div>
          <div class="mb-3">
            <label for="proposed_time" class="form-label">Hora propuesta</label>
            <input type="time" class="form-control" name="proposed_time" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Enviar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- MODAL: Finalizar recolección --}}
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="completeForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Finalizar Recolección</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <label for="confirmed_weight" class="form-label">Peso real recolectado (kg)</label>
          <input type="number" step="0.01" min="0.1" name="confirmed_weight" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Finalizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
    const DONATIONS_URL = "{{ route('collector.donations.map.data') }}";
</script>
<script src="{{ asset('js/collector-donations-map.js') }}"></script>
@endsection
