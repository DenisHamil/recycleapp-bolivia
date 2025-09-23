@extends('admin.layout')

@section('content')
    <h2 class="mb-4">🌍 Mapa de usuarios</h2>

    <div id="allUsersMap" style="height: 600px;" class="rounded shadow border"></div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        window.donors = @json($donors);
        window.collectors = @json($collectors);
    </script>
    <script src="{{ asset('js/maps/all-users-map.js') }}"></script>
@endpush
