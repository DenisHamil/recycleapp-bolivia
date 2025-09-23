@extends('collector.layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

<div class="container mt-4">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-success fw-bold">Editar Perfil Recolector</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('collector.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <!-- Imagen de perfil -->
            <div class="text-center mb-4">
                <label for="profileImageInput">
                    <img id="profilePreview"
                         src="{{ $collector->profile_image_path ? asset($collector->profile_image_path) : asset('images/default-profile.png') }}"
                         alt="Foto de perfil"
                         class="rounded-circle shadow border"
                         style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;">
                </label>
                <input type="file" name="profile_image" id="profileImageInput" class="d-none" accept="image/*">
                <p class="mt-2 text-muted">Haz clic en la imagen para cambiarla</p>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $collector->first_name) }}" required>
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $collector->last_name) }}">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $collector->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Dejar en blanco si no deseas cambiarla" id="password">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                            👁️
                        </button>
                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Repetir contraseña" id="password_confirmation">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation', this)">
                            👁️
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mapa para ubicación -->
            <div class="card mb-4">
                <div class="card-header bg-light fw-bold">Ubicación</div>
                <div class="card-body">
                    <div id="map" style="height: 300px; border-radius: 8px;"></div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $collector->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $collector->longitude) }}">
                    <small class="text-muted">Haz clic en el mapa para ajustar tu ubicación.</small>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Vista previa de imagen
    document.getElementById('profileImageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('profilePreview').src = URL.createObjectURL(file);
        }
    });

    // Función mostrar/ocultar contraseña
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
            btn.textContent = "🙈";
        } else {
            input.type = "password";
            btn.textContent = "👁️";
        }
    }

    // Mapa con Leaflet
    const map = L.map('map').setView([
        {{ $collector->latitude ?? -17.7833 }},
        {{ $collector->longitude ?? -63.1821 }}
    ], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.circleMarker([{{ $collector->latitude ?? -17.7833 }}, {{ $collector->longitude ?? -63.1821 }}], {
        radius: 10,
        color: '#28a745',
        fillColor: '#28a745',
        fillOpacity: 0.9
    }).addTo(map);

    function updateCoords(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        marker.setLatLng([lat, lng]);
    }

    map.on('click', function(e) {
        updateCoords(e.latlng.lat, e.latlng.lng);
    });
</script>
@endsection
