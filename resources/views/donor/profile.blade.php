@extends('donor.layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

<div class="container">
    <h1 class="mb-4 text-success fw-bold">Editar Perfil</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('donor.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 shadow-sm rounded">
        @csrf

        <!-- Imagen de perfil -->
        <div class="text-center mb-4">
            <label for="profileImageInput">
                <img id="profilePreview"
                     src="{{ $user->profile_image_path ? asset($user->profile_image_path) : asset('images/default-profile.png') }}"
                     alt="Foto de perfil"
                     class="rounded-circle shadow border"
                     style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;">
            </label>
            <input type="file" name="profile_image" id="profileImageInput" class="d-none" accept="image/*">
            <p class="mt-2 text-muted">Haz clic en la imagen para cambiarla</p>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        @if ($user->donor_type === 'organization')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre de la Organización</label>
                    <input type="text" name="organization_name" value="{{ old('organization_name', $user->organization_name) }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Responsable</label>
                    <input type="text" name="representative_name" value="{{ old('representative_name', $user->representative_name) }}" class="form-control">
                </div>
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nueva contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control"
                           placeholder="Dejar en blanco si no deseas cambiarla">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                        👁️
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar nueva contraseña</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" placeholder="Repetir contraseña">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation', this)">
                        👁️
                    </button>
                </div>
            </div>
        </div>

        <!-- Mapa en tarjeta -->
        <div class="card mb-4">
            <div class="card-header bg-light fw-bold">Ubicación</div>
            <div class="card-body">
                <div id="map" style="height: 300px; border-radius: 8px;"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $user->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $user->longitude) }}">
                <small class="text-muted">Haz clic en el mapa para ajustar tu ubicación.</small>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success px-4">Guardar Cambios</button>
        </div>
    </form>
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
        {{ $user->latitude ?? -17.7833 }},
        {{ $user->longitude ?? -63.1821 }}
    ], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.circleMarker([{{ $user->latitude ?? -17.7833 }}, {{ $user->longitude ?? -63.1821 }}], {
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
