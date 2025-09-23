<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>RecycleApp Recolector</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard-collector.css') }}">
</head>

<body style="background: linear-gradient(to bottom right, #e0f7fa, #c8e6c9); min-height: 100vh;">

    @php

        $user = auth()->user();
        $unreadCount = $unreadCount ?? 0;
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('collector.dashboard') }}">
                🚛 RecycleApp - Recolector
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Mostrar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto gap-2 align-items-center">

                    <!-- Inicio -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('collector.dashboard') }}">
                            <i class="bi bi-house-door-fill me-1"></i>Inicio
                        </a>
                    </li>

                    <!-- Mapa de Donaciones -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('collector.donations') }}">
                            <i class="bi bi-map-fill me-1"></i>Mapa de Donaciones
                        </a>
                    </li>

                    <!-- Ranking -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('collector.ranking') }}">
                            <i class="bi bi-trophy-fill me-1"></i>Ranking
                        </a>
                    </li>

                    <!-- Historial -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('collector.history') }}">
                            <i class="bi bi-clock-history me-1"></i>Historial
                        </a>
                    </li>

                    <!-- Notificaciones -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('collector.notifications.index') }}">
                            <i class="bi bi-bell-fill me-1"></i>Notificaciones

                            @auth
                                @if ($unreadCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">Notificaciones nuevas</span>
                                    </span>
                                @endif
                            @endauth
                        </a>
                    </li>


                    <!-- Perfil -->
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="perfilDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $user->profile_image_path ? asset($user->profile_image_path) : asset('images/default-profile.png') }}"
                                    class="rounded-circle me-2 border border-light" width="36" height="36"
                                    alt="Perfil">
                                <span class="text-white">{{ $user->first_name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('collector.profile.edit') }}">
                                        <i class="bi bi-pencil-square me-1"></i>Editar Perfil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth

                </ul>
            </div>
        </div>
    </nav>

    <main class="container mb-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
