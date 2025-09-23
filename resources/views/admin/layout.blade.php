<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel Admin | RecycleApp')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/iaom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-rewards.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>

<div class="d-flex">
    {{-- Sidebar de navegación --}}
    <nav class="bg-dark text-white p-3 vh-100" style="width: 250px;">
        <h4 class="text-center mb-3">RecycleApp Admin</h4>
        <ul class="nav flex-column mt-2">
            <li class="nav-item mb-2">
                <a href="{{ route('admin.dashboard') }}" class="nav-link text-white">📊 Panel principal</a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.approvals') }}" class="nav-link text-white">✅ Aprobaciones</a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.categories') }}" class="nav-link text-white">♻️ Categorías y puntos</a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.map') }}" class="nav-link text-white">🗺️ Mapa global</a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.reports') }}" class="nav-link text-white">📈 Reportes</a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.feedback') }}" class="nav-link text-white">✉️ Feedback</a>
            </li>
            <li class="nav-item mb-2">

                <a href="{{ route('admin.rewards.index') }}" class="nav-link text-white">🛍️ Recompensas</a>
            </li>
        </ul>
    </nav>

    <main class="flex-grow-1 p-4">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin.js') }}" defer></script>

<script src="{{ asset('js/admin-rewards.js') }}" defer></script>

@stack('scripts')

@stack('modals')

</body>
</html>
