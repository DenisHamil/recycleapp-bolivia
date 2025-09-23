@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>♻️ Categorías de residuos</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        ➕ Añadir categoría
    </button>
</div>

<table class="table table-bordered bg-white shadow-sm">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Nombre del residuo</th>
            <th>Puntos por kilo</th>
            <th>Subcategorías</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($categories as $category)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <span class="d-inline-flex align-items-center">
                        <span class="color-circle me-2" style="background-color: {{ $category->color ?? '#ccc' }}"></span>
                        {{ $category->name }}
                    </span>
                </td>
                <td>{{ $category->points_per_kilo }}</td>
                <td>
                    @if($category->subcategories->count())
                        <ul class="mb-2 ps-3">
                            @foreach($category->subcategories as $sub)
                                <li class="d-flex justify-content-between align-items-center">
                                    <span>{{ $sub->name }}</span>
                                    <form action="{{ route('admin.subcategories.delete', $sub->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger ms-2"
                                                onclick="return confirm('¿Eliminar subcategoría?')">🗑️</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <em class="text-muted">Sin subcategorías</em>
                    @endif

                    <form action="{{ route('admin.subcategories.store') }}" method="POST" class="mt-2 d-flex gap-2">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Nueva subcategoría" required>
                        <button type="submit" class="btn btn-sm btn-success">➕</button>
                    </form>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#editCategoryModal{{ $category->id }}">
                        ✏️ Editar
                    </button>

                    <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar categoría?')">🗑️</button>
                    </form>
                </td>
            </tr>

            {{-- Modal de edición --}}
            @push('modals')
            <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1"
                 aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true"
                 data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCategoryModalLabel{{ $category->id }}">Editar categoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name{{ $category->id }}" class="form-label">Nombre</label>
                                <input type="text" id="name{{ $category->id }}" name="name" class="form-control" value="{{ $category->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="points{{ $category->id }}" class="form-label">Puntos por kilo</label>
                                <input type="number" id="points{{ $category->id }}" name="points_per_kilo" class="form-control" value="{{ $category->points_per_kilo }}" required min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Color</label>
                                <input type="hidden" name="color" id="colorPickerEdit{{ $category->id }}" value="{{ $category->color ?? '#00cc99' }}">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(['#00cc99','#3399ff','#ff9900','#cc0066','#6666ff','#33cccc','#99cc00','#ff6666','#ffcc00','#cc9933'] as $color)
                                        <div class="color-circle"
                                             style="background-color: {{ $color }}"
                                             onclick="selectColor('{{ $color }}', 'colorPickerEdit{{ $category->id }}')">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary" type="submit">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
            @endpush
        @empty
            <tr>
                <td colspan="5" class="text-center">No hay categorías registradas.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Modal para crear nueva categoría --}}
@push('modals')
<div class="modal fade" id="createCategoryModal" tabindex="-1"
     aria-labelledby="createCategoryModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Añadir nueva categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="points_per_kilo" class="form-label">Puntos por kilo</label>
                    <input type="number" name="points_per_kilo" id="points_per_kilo" class="form-control" required min="0" step="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <input type="hidden" name="color" id="colorPickerCreate" value="#00cc99">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['#00cc99','#3399ff','#ff9900','#cc0066','#6666ff','#33cccc','#99cc00','#ff6666','#ffcc00','#cc9933'] as $color)
                            <div class="color-circle"
                                 style="background-color: {{ $color }}"
                                 onclick="selectColor('{{ $color }}', 'colorPickerCreate')">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" type="submit">Crear categoría</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endsection
