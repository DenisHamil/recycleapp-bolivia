@extends('admin.layout')

@section('title', 'Recompensas')

@section('content')
@php
    $isHistory = request()->boolean('only_trashed');
@endphp

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Recompensas</h1>
    <a class="btn btn-primary" href="{{ route('admin.rewards.create') }}">Nueva recompensa</a>
</div>

{{-- Tabs Activas / Historial --}}
<div class="mb-3">
    <a href="{{ route('admin.rewards.index') }}"
       class="btn {{ !$isHistory ? 'btn-dark' : 'btn-outline-dark' }}">Activas</a>
    <a href="{{ route('admin.rewards.index', ['only_trashed' => 1]) }}"
       class="btn {{ $isHistory ? 'btn-dark' : 'btn-outline-dark' }}">Historial</a>
</div>

{{-- Filtros simples --}}
<form method="GET" action="{{ route('admin.rewards.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por nombre...">
    </div>
    <div class="col-md-3">
        <select name="category" class="form-select">
            <option value="">Todas las categorías</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="commerce" class="form-select">
            <option value="">Todos los comercios</option>
            @foreach($commerces as $co)
                <option value="{{ $co->id }}" @selected(request('commerce')==$co->id)>{{ $co->name }}</option>
            @endforeach
        </select>
    </div>
    {{-- Mantener pestaña actual al filtrar --}}
    <input type="hidden" name="only_trashed" value="{{ $isHistory ? 1 : 0 }}">
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100">Filtrar</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:70px;">Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Comercio</th>
                        <th class="text-center">Puntos</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Estado</th>
                        <th style="width:280px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rewards as $r)
                    @php
                        $thumb = optional($r->images->first())->path;
                    @endphp
                    <tr @class(['table-secondary'=> $r->trashed()])>
                        <td>
                            @if($thumb)
                                <img src="{{ asset('storage/'.$thumb) }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                            @else
                                <div style="width:56px;height:56px;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;">—</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $r->name }}</div>
                            @if($r->description)
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($r->description, 80) }}</div>
                            @endif
                        </td>
                        <td>{{ $r->category->name ?? '—' }}</td>
                        <td>{{ $r->commerce->name ?? '—' }}</td>
                        <td class="text-center">{{ number_format($r->points_required) }}</td>
                        <td class="text-center">{{ number_format($r->stock) }}</td>
                        <td class="text-center">
                            @if($r->trashed())
                                <span class="badge bg-secondary">Archivada</span>
                            @elseif($r->stock > 0)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-warning text-dark">Sin stock</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.rewards.show', $r->id) }}">Ver</a>

                                @if(!$r->trashed())
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.rewards.edit', $r->id) }}">Editar</a>

                                    {{-- Eliminar: si tiene canjes -> archiva; si no -> borra definitivo (controlador ya lo maneja) --}}
                                    <form method="POST" action="{{ route('admin.rewards.destroy', $r->id) }}"
                                          onsubmit="return confirm('¿Eliminar? Si tiene canjes, se archivará; si no, se borrará definitivamente.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                    </form>
                                @else
                                    {{-- Archivada: Restaurar --}}
                                    <form method="POST" action="{{ route('admin.rewards.restore', $r->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success" type="submit">Restaurar</button>
                                    </form>

                                    {{-- Eliminación definitiva desde historial (solo si no tiene canjes; el controlador valida) --}}
                                    <form method="POST" action="{{ route('admin.rewards.destroy', $r->id) }}"
                                          onsubmit="return confirm('¿Eliminar definitivamente? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar definitivamente</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            @if($isHistory)
                                No hay recompensas en el historial.
                            @else
                                No hay recompensas activas.
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($rewards, 'links'))
        <div class="card-footer">
            {{ $rewards->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
