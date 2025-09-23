@extends('admin.layout')
@section('title', 'Editar recompensa')

@section('content')
<div class="container">
  <h1>Editar recompensa</h1>

  @if (session('success'))
    <div class="panel">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="panel danger">
      <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="panel">
    <form method="POST" action="{{ route('admin.rewards.update', $reward->id) }}" enctype="multipart/form-data" class="form-grid">
      @csrf @method('PUT')

      <div class="full">
        <label>Nombre *</label>
        <input type="text" name="name" value="{{ old('name', $reward->name) }}" required>
      </div>

      <div class="full">
        <label>Descripción</label>
        <textarea name="description" rows="4">{{ old('description', $reward->description) }}</textarea>
      </div>

      <div>
        <label>Categoría *</label>
        <select name="reward_category_id" required>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('reward_category_id', $reward->reward_category_id)==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label>Comercio</label>
        <select name="commerce_id">
          <option value="">— Ninguno —</option>
          @foreach($commerces as $v)
            <option value="{{ $v->id }}" @selected(old('commerce_id', $reward->commerce_id)==$v->id)>{{ $v->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label>Puntos requeridos *</label>
        <input type="number" min="1" name="points_required" value="{{ old('points_required', $reward->points_required) }}" required>
      </div>

      <div>
        <label>Stock *</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', $reward->stock) }}" required>
      </div>

      <div class="full">
        <label><input type="checkbox" name="is_monthly_promo" value="1" @checked(old('is_monthly_promo', $reward->is_monthly_promo))> ¿Promoción mensual?</label>
      </div>

      <div class="full">
        <label>Imágenes actuales</label>
        <div class="gallery">
          @forelse($reward->images as $img)
            @php
              $imgPath = $img->path 
                  ? asset('storage/rewards/' . basename($img->path))
                  : null;
            @endphp
            @if($imgPath)
              <div class="thumb"><img src="{{ $imgPath }}" alt=""></div>
            @endif
          @empty
            <div class="sub">— Sin imágenes —</div>
          @endforelse
        </div>
        <div class="sub">Las imágenes existentes se mantienen. Si subís nuevas, se agregan.</div>
      </div>

      <div class="full">
        <label>Agregar nuevas imágenes</label>
        <div id="ar-drop" class="drop">Soltá aquí las imágenes o hacé click abajo</div>
        <input type="file" name="image[]" multiple accept="image/*">
        <div id="ar-preview" class="gallery" style="margin-top:8px"></div>
      </div>

      <div class="full actions">
        <button class="btn primary" type="submit">Actualizar</button>
        <a class="btn" href="{{ route('admin.rewards.index') }}">Volver</a>
      </div>
    </form>
  </div>
</div>
@endsection
