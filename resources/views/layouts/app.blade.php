<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RecycleApp Bolivia')</title>

    <!-- Bootstrap y Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos globales -->
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    @stack('styles')
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success px-4 shadow-sm">
        <div class="container-fluid">
            <a href="{{ route('landing') }}" class="navbar-brand fw-bold">RecycleApp Bolivia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="{{ route('landing') }}" class="nav-link text-white">Inicio</a></li>
                    <li class="nav-item"><a href="{{ route('register.choose') }}" class="nav-link text-white">Registrarse</a></li>
                    <li class="nav-item"><a href="{{ route('login') }}" class="nav-link text-white">Iniciar sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    @yield('content')

    <!-- Scripts base -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personalizados -->
    @yield('scripts')
</body>
</html>
