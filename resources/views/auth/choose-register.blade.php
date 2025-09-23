@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
@endpush

@section('content')
<div class="container text-center py-5">
    <h1 class="mb-4 fw-bold">¿Cómo deseas registrarte?</h1>
    <p class="lead text-muted mb-5">Selecciona el tipo de usuario que mejor se adapte a ti para continuar con tu registro.</p>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 landing-card">
                <div class="card-body d-flex flex-column align-items-center">
                    <i class="bi bi-house-heart-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title fw-semibold">Registrarme como Donador</h5>
                    <p class="card-text text-muted">Ideal si eres una familia o una organización que desea donar residuos reciclables.</p>
                    <a href="{{ route('register.donor') }}" class="btn btn-success mt-auto">
                        <i class="bi bi-arrow-right-circle-fill me-1"></i> Continuar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 landing-card">
                <div class="card-body d-flex flex-column align-items-center">
                    <i class="bi bi-truck-front-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title fw-semibold">Registrarme como Recolector</h5>
                    <p class="card-text text-muted">Si representas a una empresa recolectora, esta opción es para ti.</p>
                    <a href="{{ route('register.collector') }}" class="btn btn-success mt-auto">
                        <i class="bi bi-arrow-right-circle-fill me-1"></i> Continuar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
