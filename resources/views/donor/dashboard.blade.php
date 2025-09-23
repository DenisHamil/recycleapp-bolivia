@extends('donor.layouts.app')

@section('content')
@php
    $user = Auth::user();
@endphp

<link rel="stylesheet" href="{{ asset('css/dashboard-donor.css') }}">

<div class="container text-center">
    <h1 class="mb-5 fw-bold text-success">Bienvenido, {{ $user->first_name }} 👋</h1>

    <!-- Publicar Donación -->
    <div class="donor-card bg-light-green mb-4">
        <div class="donor-card-icon">📦</div>
        <h5 class="fw-bold text-success">Publicar Donación</h5>
        <p>Registra una nueva donación para que los recolectores puedan verla en su mapa.</p>
        <a href="{{ route('donor.donations.create') }}" class="btn btn-success">Ir al formulario</a>
    </div>

    <!-- Ver Ranking -->
    <div class="donor-card bg-light-yellow mb-4">
        <div class="donor-card-icon">🏆</div>
        <h5 class="fw-bold text-warning">Ver Ranking</h5>
        <p>Consulta tu posición y la de otros usuarios en el ranking de donadores.</p>
        <a href="{{ route('donor.ranking') }}" class="btn btn-warning text-white">Ver Ranking</a>
    </div>

    <!-- Tienda de Recompensas -->
    <div class="donor-card bg-light-pink mb-4">
        <div class="donor-card-icon">🎁</div>
        <h5 class="fw-bold text-danger">Tienda de Recompensas</h5>
        <p>Gasta tus puntos acumulados en productos y servicios disponibles.</p>
        <a href="{{ route('donor.store') }}" class="btn btn-danger">Entrar a la tienda</a>
    </div>
</div>
@endsection
