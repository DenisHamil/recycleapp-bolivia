@extends('admin.layout')

@section('content')
    <h2 class="mb-4">📊 Panel de Administrador</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-success h-100">
                <div class="card-body">
                    <h5 class="card-title">✅ Aprobaciones pendientes</h5>
                    <p class="card-text">Revisa y aprueba empresas recolectoras registradas.</p>
                    <a href="{{ route('admin.approvals') }}" class="btn btn-success">Ir a Aprobaciones</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">♻️ Categorías y puntos</h5>
                    <p class="card-text">Define los residuos disponibles y sus valores por kilo.</p>
                    <a href="{{ route('admin.categories') }}" class="btn btn-primary">Gestionar categorías</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info h-100">
                <div class="card-body">
                    <h5 class="card-title">🗺️ Mapa de usuarios</h5>
                    <p class="card-text">Visualiza a todos los donadores y recolectores en el mapa.</p>
                    <a href="{{ route('admin.map') }}" class="btn btn-info">Ver mapa</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <h5 class="card-title">📈 Reportes</h5>
                    <p class="card-text">Consulta estadísticas del sistema y rendimiento.</p>
                    <a href="{{ route('admin.reports') }}" class="btn btn-warning">Ver reportes</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-danger h-100">
                <div class="card-body">
                    <h5 class="card-title">✉️ Feedback</h5>
                    <p class="card-text">Lee sugerencias y comentarios de los usuarios.</p>
                    <a href="{{ route('admin.feedback') }}" class="btn btn-danger">Ver feedback</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-dark h-100">
                <div class="card-body">
                    <h5 class="card-title">🛍️ Recompensas</h5>
                    <p class="card-text">Gestiona las recompensas disponibles y sus canjes.</p>
                    <a href="{{ route('admin.rewards.index') }}" class="btn btn-dark">Ver recompensas</a>
                </div>
            </div>
        </div>
    </div>
@endsection
