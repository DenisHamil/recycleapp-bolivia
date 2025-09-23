@extends('admin.layout')

@section('content')
    <h2 class="mb-4">✅ Aprobación de empresas recicladoras</h2>

    {{-- Mostrar mensajes flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="row">
        @forelse ($collectors as $collector)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h5 class="mb-0 text-success"><i class="bi bi-building"></i> {{ $collector->company_name }}</h5>
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#collapse{{ $collector->id }}">
                            Ver más
                        </button>
                    </div>

                    <div class="collapse show" id="collapse{{ $collector->id }}">
                        <div class="card-body">
                            <ul class="list-unstyled mb-3">
                                <li><strong>Representante:</strong> {{ $collector->representative_name }}</li>
                                <li><strong>Email:</strong> {{ $collector->email }}</li>
                                <li><strong>Dirección:</strong> {{ $collector->address }}</li>
                                <li><strong>Ubicación:</strong> {{ $collector->department }} - {{ $collector->province }} - {{ $collector->municipality }}</li>
                            </ul>

                            <div id="map{{ $collector->id }}" class="rounded border" style="height: 200px;"></div>

                            <div class="mt-3 d-flex justify-content-between">
                                <form action="{{ route('admin.approvals.accept', $collector->id) }}" method="POST" class="me-1 w-100">
                                    @csrf
                                    <button class="btn btn-success w-100">
                                        ✅ Aprobar
                                    </button>
                                </form>
                                <form action="{{ route('admin.approvals.reject', $collector->id) }}" method="POST" class="ms-1 w-100">
                                    @csrf
                                    <button class="btn btn-danger w-100">
                                        ❌ Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info col-12">
                No hay empresas pendientes de aprobación en este momento.
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        window.collectors = @json($collectors);
    </script>
    <script src="{{ asset('js/maps/map.js') }}"></script>
@endpush
