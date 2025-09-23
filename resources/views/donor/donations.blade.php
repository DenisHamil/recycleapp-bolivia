@extends('donor.layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/donation-create.css') }}">
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

<div class="container">
    <h1 class="mb-4">Publicar Donación</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('donor.donations.store') }}" enctype="multipart/form-data" class="p-4 rounded shadow bg-light">
        @csrf

        <div class="mb-3">
            <label class="form-label">Categoría de residuo</label>
            <select name="category_id" id="categorySelect" class="form-select" required>
                <option value="" disabled selected>Seleccione una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" data-color="{{ $category->color }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Ej: Botellas plásticas, papeles, cartón..." required></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Peso aproximado (kg)</label>
                <input type="number" name="estimated_weight" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Imagen del residuo</label>
                <input type="file" name="image" accept="image/*" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Desde (fecha disponible)</label>
                <input type="date" name="available_from_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hasta (fecha disponible)</label>
                <input type="date" name="available_until_date" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Desde (hora disponible)</label>
                <input type="time" name="available_from_time" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hasta (hora disponible)</label>
                <input type="time" name="available_until_time" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción de dirección (opcional)</label>
            <input type="text" name="address_description" class="form-control" placeholder="Ej: Calle Junín, esquina Bolívar, zona centro">
        </div>

        <div class="mb-3">
            <label class="form-label">Ubicación</label>
            <div id="map" style="height: 300px; border-radius: 8px;"></div>
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', Auth::user()->latitude) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', Auth::user()->longitude) }}">
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success">Publicar Donación</button>
        </div>
    </form>
</div>

<script>
    const map = L.map('map').setView([
        {{ Auth::user()->latitude ?? -17.7833 }},
        {{ Auth::user()->longitude ?? -63.1821 }}
    ], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.circleMarker(map.getCenter(), {
        radius: 10,
        color: '#28a745',
        fillColor: '#28a745',
        fillOpacity: 0.8
    }).addTo(map);

    // Asignar coordenadas iniciales al pin (basado en usuario)
    document.getElementById('latitude').value = map.getCenter().lat;
    document.getElementById('longitude').value = map.getCenter().lng;

    // Desactivar movimiento del pin en esta vista
    // map.on('click', function(e) {
    //     updateCoords(e.latlng.lat, e.latlng.lng);
    // });

    // Función para cambiar el color del pin
    function changeMarkerColor(color) {
        marker.setStyle({
            color: color,
            fillColor: color
        });
    }

    // Detectar cambio de categoría y actualizar color del pin
    document.getElementById('categorySelect').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const color = selectedOption.getAttribute('data-color');
        if (color) {
            changeMarkerColor(color);
        }
    });
</script>
@endsection
