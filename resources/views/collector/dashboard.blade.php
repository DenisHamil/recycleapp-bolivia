@extends('collector.layouts.app')

@section('content')
@php $user = Auth::user(); @endphp

<div class="container text-center">
    <h1 class="mb-5 fw-bold text-primary">¡Hola, {{ $user->first_name }}! 👋</h1>

    <!-- Mapa de Donaciones -->
    <div class="donor-card bg-light mb-4 shadow-sm p-4 rounded">
        <div class="donor-card-icon fs-1 mb-2">🗺️</div>
        <h5 class="fw-bold text-primary">Ver Donaciones</h5>
        <p>Explora en el mapa las donaciones disponibles cerca de ti.</p>
        <a href="{{ route('collector.donations') }}" class="btn btn-primary">Ir al Mapa</a>
    </div>

    <!-- Ver Ranking -->
    <div class="donor-card bg-light mb-4 shadow-sm p-4 rounded">
        <div class="donor-card-icon fs-1 mb-2">🏆</div>
        <h5 class="fw-bold text-warning">Ver Ranking</h5>
        <p>Consulta tu posición y la de otros recolectores o donadores.</p>
        <a href="{{ route('collector.ranking') }}" class="btn btn-warning text-white">Ver Ranking</a>
    </div>
</div>
@endsection
