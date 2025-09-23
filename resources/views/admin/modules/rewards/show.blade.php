@extends('admin.layout')
@section('title', 'Detalle de recompensa')

@section('content')
<div class="container">
  <h1>Detalle de recompensa</h1>

  <div class="panel">
    <div class="form-grid">
      <div class="full">
        <strong>Nombre:</strong> {{ $reward->name }}
      </div>

      <div class="full">
        <strong>Descripción:</strong>
        <p>{{ $reward->description ?: '—' }}</p>
      </div>

      <div>
        <strong>Categoría:</strong>
        {{ $reward->category->name ?? '—' }}
      </div>

      <div>
        <strong>Comercio:</strong>
        {{ $reward->commerce->name ?? '—' }}
      </div>

      <div>
        <strong>Puntos requeridos:</strong>
        {{ (int) $reward->points_required }}
      </div>

      <div>
        <strong>Stock:</strong>
        {{ is_null($reward->stock) ? '∞' : (int) $reward->stock }}
      </div>

      <div>
        <strong>Promoción mensual:</strong>
        {{ $reward->is_monthly_promo ? 'Sí' : 'No' }}
      </div>

      <div class="full">
        <strong>Imágenes:</strong>
        @if($reward->images->count())
          <div class="gallery">
            @foreach($reward->images as $img)
              @php
                $imgPath = $img->path 
                    ? asset('storage/rewards/' . basename($img->path))
                    : null;
              @endphp
              @if($imgPath)
                <div class="thumb">
                  <img src="{{ $imgPath }}" alt="">
                </div>
              @endif
            @endforeach
          </div>
        @else
          <div class="sub">— Sin imágenes —</div>
        @endif
      </div>

      <div class="full actions">
        <a class="btn" href="{{ route('admin.rewards.edit', $reward->id) }}">Editar</a>
        <a class="btn" href="{{ route('admin.rewards.index') }}">Volver</a>
      </div>
    </div>
  </div>
</div>
@endsection
