@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="card landing-card shadow-sm border-0 p-4 mx-auto" style="max-width: 800px;">
        <h2 class="text-center text-success fw-bold mb-4">
            <i class="bi bi-truck me-2"></i> Registro de Recolector (Empresa)
        </h2>

        {{-- ✅ Mostrar errores --}}
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

        {{-- ✅ Formulario --}}
        <form action="{{ route('register.collector.store') }}" method="POST" enctype="multipart/form-data" id="collectorForm">
            @csrf

            <!-- Imagen de perfil -->
            <div class="text-center mb-3">
                <label for="profile_image">
                    <img src="{{ asset('images/default-profile.png') }}" id="preview" class="profile-preview" alt="Foto de perfil" style="cursor:pointer">
                </label>
                <input type="file" name="profile_image_path" id="profile_image" class="d-none" accept="image/*">
            </div>

            <!-- Nombre de empresa -->
            <div class="mb-3">
                <label for="company_name" class="form-label">Nombre de la empresa</label>
                <input type="text" name="company_name" class="form-control" required>
            </div>

            <!-- Representante -->
            <div class="mb-3">
                <label for="representative_name" class="form-label">Nombre del responsable</label>
                <input type="text" name="representative_name" class="form-control" required>
            </div>

            <!-- Email y contraseña -->
            <div class="row mb-3">
                <div class="col">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col position-relative">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span class="input-group-text toggle-password" data-target="password">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Confirmar contraseña -->
            <div class="mb-3 position-relative">
                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    <span class="input-group-text toggle-password" data-target="password_confirmation">
                        <i class="bi bi-eye-slash" id="togglePasswordConfirmIcon"></i>
                    </span>
                </div>
            </div>

            <!-- Dirección general -->
            <div class="row mb-3">
                <div class="col">
                    <label for="department" class="form-label">Departamento</label>
                    <select name="department" id="department" class="form-select" required>
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <div class="col">
                    <label for="province" class="form-label">Provincia</label>
                    <select name="province" id="province" class="form-select" required>
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="municipality" class="form-label">Municipio</label>
                <select name="municipality" id="municipality" class="form-select" required>
                    <option value="">Seleccionar...</option>
                </select>
            </div>

            <!-- Dirección específica -->
            <div class="mb-3">
                <label for="address" class="form-label">Dirección (calle, zona, referencia)</label>
                <input type="text" name="address" id="address" class="form-control" required>
            </div>

            <!-- Categorías dinámicas -->
            <div class="mb-3">
                <label for="category_id" class="form-label">Categorías que recolecta</label>
                <div>
                    @foreach($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input category-checkbox" type="checkbox" name="category_id[]" value="{{ $category->id }}" id="category_{{ $category->id }}">
                            <label class="form-check-label" for="category_{{ $category->id }}">
                                {{ $category->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div>
                    <input class="form-check-input" type="checkbox" id="select-all-categories">
                    <label for="select-all-categories">Seleccionar todas las categorías</label>
                </div>
            </div>

            <!-- Mapa interactivo -->
            <div class="mb-3">
                <label class="form-label">Ubicación (haz clic en el mapa)</label>
                <div id="map" class="leaflet-map"></div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>

            <!-- Botón de envío -->
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
    <script>
        // Vista previa al seleccionar imagen
        document.getElementById('profile_image').addEventListener('change', function (event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('preview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
@endsection
