@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="card landing-card shadow-sm border-0 p-4 mx-auto" style="max-width: 700px;">
        <h2 class="text-center text-success fw-bold mb-4">
            <i class="bi bi-person-plus-fill me-2"></i> Registro de Donador
        </h2>

        {{-- ✅ Mostrar mensajes de éxito --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- ✅ Mostrar errores de validación --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <form action="{{ route('register.donor.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Imagen de perfil -->
            <div class="text-center mb-3">
                <label for="profile_image">
                    <img src="{{ asset('images/default-profile.png') }}" id="preview" class="profile-preview" alt="Foto de perfil">
                </label>
                <input type="file" name="profile_image_path" id="profile_image" class="d-none" accept="image/*">
            </div>

            <!-- Nombre y Apellido -->
            <div class="row mb-3">
                <div class="col">
                    <label for="first_name" class="form-label">Nombre</label>
                    <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
                </div>
                <div class="col">
                    <label for="last_name" class="form-label">Apellido</label>
                    <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
                </div>
            </div>

            <!-- Email y Contraseña -->
            <div class="row mb-3">
                <div class="col">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="col">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span class="input-group-text toggle-password" data-target="password" style="cursor:pointer">
                            <i class="bi bi-eye-slash" id="icon-password"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Confirmar contraseña -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    <span class="input-group-text toggle-password" data-target="password_confirmation" style="cursor:pointer">
                        <i class="bi bi-eye-slash" id="icon-password_confirmation"></i>
                    </span>
                </div>
            </div>

            <!-- Tipo de donador -->
            <div class="mb-3">
                <label for="donor_type" class="form-label">Tipo de donador</label>
                <select name="donor_type" id="donor_type" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option value="family" {{ old('donor_type') === 'family' ? 'selected' : '' }}>Familia</option>
                    <option value="organization" {{ old('donor_type') === 'organization' ? 'selected' : '' }}>Organización</option>
                </select>
            </div>

            <!-- Campo condicional: organización -->
            <div id="organizationFields" class="{{ old('donor_type') === 'organization' ? '' : 'd-none' }}">
                <div class="mb-3">
                    <label for="organization_name" class="form-label">Nombre de la organización</label>
                    <input type="text" name="organization_name" class="form-control" value="{{ old('organization_name') }}">
                </div>
                <div class="mb-3">
                    <label for="representative_name" class="form-label">Nombre del responsable</label>
                    <input type="text" name="representative_name" class="form-control" value="{{ old('representative_name') }}">
                </div>
            </div>

            <!-- Mapa interactivo -->
            <div class="mb-3">
                <label class="form-label">Ubicación (selecciona en el mapa)</label>
                <div id="map" class="leaflet-map"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
            </div>

            <!-- Dirección geográfica -->
            <div class="row mb-3">
                <div class="col">
                    <label for="department" class="form-label">Departamento</label>
                    <select name="department" id="department" class="form-select" required>
                        <option value="">{{ old('department') ? old('department') : 'Seleccionar...' }}</option>
                    </select>
                </div>
                <div class="col">
                    <label for="province" class="form-label">Provincia</label>
                    <select name="province" id="province" class="form-select" required>
                        <option value="">{{ old('province') ? old('province') : 'Seleccionar...' }}</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="municipality" class="form-label">Municipio</label>
                <select name="municipality" id="municipality" class="form-select" required>
                    <option value="">{{ old('municipality') ? old('municipality') : 'Seleccionar...' }}</option>
                </select>
            </div>

            <!-- Dirección específica -->
            <div class="mb-4">
                <label for="address" class="form-label">Dirección (calle, zona, referencia)</label>
                <input type="text" name="address" id="address" class="form-control" required value="{{ old('address') }}">
            </div>

            <!-- Botón -->
            <div class="d-grid">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle-fill me-1"></i> Registrarme
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/register.js') }}"></script>
    <script src="{{ asset('js/maps/register-map.js') }}"></script>
    <script src="{{ asset('js/location-dropdowns.js') }}"></script>
    <script src="{{ asset('js/forms/password-toggle.js') }}"></script>
@endsection
