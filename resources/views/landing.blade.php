@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
@endpush

@section('content')
    <header class="landing-header">
        <div class="container text-center">
            <h1 class="mb-3">Bienvenido a <strong>RecycleApp Bolivia</strong></h1>
            <p class="lead">Conectamos donadores con recolectores para un futuro más limpio.</p>

            <div class="mt-4">
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg mx-2">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </a>
                <a href="{{ route('register.choose') }}" class="btn btn-success btn-lg mx-2">
                    <i class="bi bi-person-plus-fill"></i> Registrarse
                </a>
            </div>
        </div>
    </header>

    <main class="landing-content py-5">
        <div class="container">

            <div class="card landing-card mb-5 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="section-title text-success">¿Qué es RecycleApp?</h2>
                    <p>
                        RecycleApp Bolivia es una iniciativa social y tecnológica comprometida con el medio ambiente. Nuestra plataforma digital conecta donadores de residuos reciclables —familias, organizaciones y empresas— con recolectores responsables, fomentando una red de colaboración activa para la gestión adecuada de residuos sólidos. 
                    </p>
                    <p>
                        Promovemos la economía circular, la sostenibilidad urbana y la educación ambiental, aportando a una Bolivia más limpia, consciente y unida en torno al reciclaje. Juntos, transformamos residuos en oportunidades para construir comunidades más verdes.
                    </p>
                </div>
            </div>

            <div class="card landing-card mb-5 shadow-sm">
                <div class="card-body">
                    <h3 class="section-title text-primary text-center">¿Cómo funciona?</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>👨‍👩‍👧 Donadores:</h5>
                            <ul>
                                <li>Regístrate como familia u organización</li>
                                <li>Publica tus residuos disponibles</li>
                                <li>Recibe propuestas de recolectores</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>🚛 Recolectores:</h5>
                            <ul>
                                <li>Regístrate como empresa recolectora</li>
                                <li>Especialízate por categoría</li>
                                <li>Postula a donaciones disponibles</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card landing-card mb-4 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="section-title text-success">🎁 Gana puntos y canjéalos por recompensas</h4>
                    <p>
                        Cada vez que se finaliza una donación con éxito, el donador acumula puntos que podrá canjear en la tienda de recompensas ecológicas: desde productos reutilizables hasta descuentos en comercios aliados.
                    </p>
                </div>
            </div>

        </div>
    </main>

    <footer class="landing-footer text-white text-center py-3">
        © {{ date('Y') }} RecycleApp Bolivia — Reciclando con propósito
    </footer>
@endsection
