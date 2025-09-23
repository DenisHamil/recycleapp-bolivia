@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
@endpush

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm border-0 p-4 landing-card" style="max-width: 450px; width: 100%;">
        <h2 class="text-center text-success mb-4 fw-bold">
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
        </h2>

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Mensajes de error --}}
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

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="ejemplo@correo.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="********" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-door-open me-1"></i> Entrar
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">¿No tienes cuenta aún?</p>
            <a href="{{ route('register.choose') }}" class="btn btn-outline-primary btn-sm mt-2">
                <i class="bi bi-person-plus me-1"></i> Registrarse
            </a>
        </div>
    </div>
</div>
@endsection
