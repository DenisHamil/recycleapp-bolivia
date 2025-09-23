<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>RecycleApp Donador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-donor.css') }}">
</head>

<body style="background: linear-gradient(to bottom right, #d4f5d4, #c8f0e0); min-height: 100vh;">

    @php
        $user = auth()->user();
        $unreadCount = $unreadCount ?? 0;
    @endphp

    <!-- ✅ NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('donor.dashboard') }}">
                ♻️ RecycleApp - Donador
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center gap-2">

                    <!-- Inicio -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('donor.dashboard') }}">
                            <i class="bi bi-house-door-fill me-1"></i>Inicio
                        </a>
                    </li>

                    <!-- Ranking -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('donor.ranking') }}">
                            <i class="bi bi-trophy-fill me-1"></i>Ranking
                        </a>
                    </li>

                    <!-- Tienda -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('donor.store') }}">
                            <i class="bi bi-gift-fill me-1"></i>Tienda
                        </a>
                    </li>

                    <!-- Historial -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('donor.history') }}">
                            <i class="bi bi-clock-history me-1"></i>Historial
                        </a>
                    </li>

                    <!-- Donar -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('donor.donations.create') }}">
                            <i class="bi bi-box2-heart-fill me-1"></i>Donar
                        </a>
                    </li>

                    <!-- Notificaciones -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('donor.notifications.index') }}">
                            <i class="bi bi-bell-fill me-1"></i>Notificaciones
                            @auth
                                @if ($unreadNotificationsCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">Nuevas notificaciones</span>
                                    </span>
                                @endif
                            @endauth
                        </a>
                    </li>


                    <!-- Perfil Dropdown -->
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="perfilDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $user->profile_image_path ? asset($user->profile_image_path) : asset('images/default-profile.png') }}"
                                    class="rounded-circle me-2 border border-light" width="36" height="36"
                                    alt="Perfil">
                                <span class="text-white">{{ $user->first_name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if (Route::has('donor.profile.edit'))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('donor.profile.edit') }}">
                                            <i class="bi bi-pencil-square me-1"></i>Editar Perfil
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @endif
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

    <!-- ✅ Contenido dinámico -->
    <main class="container mb-5">
        @yield('content') {{-- Aquí se insertará el contenido del dashboard --}}
    </main>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
