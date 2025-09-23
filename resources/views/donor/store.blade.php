@extends('donor.layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/donor-store.css') }}">
<script src="{{ asset('js/donor-store.js') }}" defer></script>

@php
    use Illuminate\Support\Str;

    $avatarPath = $user->profile_image_path;

    if ($avatarPath) {
        if (Str::startsWith($avatarPath, ['http://', 'https://'])) {
            // URL externa
            $avatarUrl = $avatarPath;
        } else {
            // Archivo en /public/profiles
            $avatarUrl = asset('profiles/' . ltrim($avatarPath, '/'));
        }
    } else {
        // Imagen por defecto si no tiene avatar
        $avatarUrl = asset('img/default-avatar.png');
    }
@endphp

<img class="avatar" src="{{ $avatarUrl }}" alt="Foto de perfil de {{ $user->first_name }}">


                <div>
                    <h1>Mi progreso</h1>
                    <div class="sub">{{ $user->first_name }} {{ $user->last_name }} · Nivel <b>{{ ucfirst($currentLevel ?? 'N/A') }}</b></div>
                </div>
            </div>
            <div class="row">
                <button class="btn ghost" data-tab-target="actividad">Historial</button>
                <button class="btn primary" data-tab-target="cupones">Mis cupones</button>
            </div>
        </div>

        <div class="row">
            <div class="col panel" style="flex:2">
                <div class="row" style="justify-content:space-between">
                    <span class="chip">Puntos</span>
                    <span class="chip">
                        @if ($nextLevel)
                            {{ $pointsToNextLevel }} pts para {{ ucfirst($nextLevel) }}
                        @else
                            Nivel máximo 🎉
                        @endif
                    </span>
                </div>
                <div class="row">
                    <div class="val">{{ $totalPoints }}</div>
                    <div class="sub">puntos acumulados</div>
                </div>
                <div class="progress" aria-label="Progreso al siguiente nivel">
                    <span style="transform:scaleX({{ max(0,min(100,$progressPercentage)) }}/100)"></span>
                </div>
                <div class="sub">Progreso al siguiente nivel: <b>{{ $progressPercentage }}%</b></div>

                <div class="kpi" style="margin-top:12px">
                    <div class="card"><div class="sub">Nivel actual</div><div class="val">{{ ucfirst($currentLevel ?? '—') }}</div></div>
                    <div class="card"><div class="sub">Redenciones</div><div class="val">{{ $redemptionsCount ?? 0 }}</div></div>
                    <div class="card"><div class="sub">Donaciones</div><div class="val">{{ $donationsCount ?? 0 }}</div></div>
                    <div class="card"><div class="sub">Ranking</div><div class="val">#{{ $rankPosition ?? '—' }}</div></div>
                </div>
            </div>

            <div class="panel col" style="flex:1">
                <div class="row" style="justify-content:space-between">
                    <span class="chip">Insignias</span>
                    <span class="sub">Ver todas</span>
                </div>
                <div class="row" style="flex-wrap:wrap">
                    @forelse(($badges ?? []) as $badge)
                        <span class="chip" title="{{ $badge['description'] ?? '' }}">{{ $badge['name'] ?? 'Insignia' }}</span>
                    @empty
                        <div class="empty">Aún no tienes insignias. ¡Sigue reciclando!</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tabs">
        <button class="tab active" data-tab-target="recompensas">Recompensas</button>
        <button class="tab" data-tab-target="cupones">Mis cupones</button>
        <button class="tab" data-tab-target="actividad">Actividad</button>
    </div>

    {{-- Layout filtros + contenido --}}
    <div class="layout">
        <aside class="panel filter">
            <h4>Filtros</h4>
            <div class="sub">Categorías</div>
            <div class="row" style="flex-wrap:wrap">
                <button class="chip js-filter-category" data-category="all">Todas</button>
                @foreach(($categories ?? []) as $cat)
                    <button class="chip js-filter-category" data-category="{{ $cat->id }}">{{ $cat->name }}</button>
                @endforeach
            </div>

            <div class="sub" style="margin-top:8px">Rango de puntos</div>
            <input class="range" id="ds-range-max" type="range" min="0" max="{{ max(1000, (int)($maxRewardCost ?? 1000)) }}" step="10">
            <div class="row" style="justify-content:space-between">
                <span class="sub">0</span>
                <span class="tag"><span id="ds-range-val"></span></span>
            </div>

            <div class="sub" style="margin-top:8px">Ordenar por</div>
            <select id="ds-sort" style="width:100%">
                <option value="popular">Más populares</option>
                <option value="new">Novedades</option>
                <option value="low">Menor costo</option>
                <option value="high">Mayor costo</option>
            </select>

            <div class="search" style="margin-top:10px">
                <input id="ds-search" type="text" placeholder="Buscar recompensa, comercio, categoría…">
                <button id="ds-search-btn" class="btn">Buscar</button>
            </div>
        </aside>

        <section>
            {{-- Recompensas --}}
            <div data-tab-content="recompensas">
                <div class="grid">
                    @forelse(($rewards ?? []) as $reward)
                        @php
                            // 1) Intento 1: colección de imágenes (tabla images)
                            $imgPath = optional($reward->images->first())->path;

                            // 2) Intento 2: columna image_path del reward
                            if (!$imgPath && !empty($reward->image_path)) {
                                $imgPath = $reward->image_path;
                            }

                            // Normalización a URL pública
                            if ($imgPath) {
                                if (Str::startsWith($imgPath, ['http://','https://'])) {
                                    $imgUrl = $imgPath;
                                } elseif (Str::contains($imgPath, 'storage/')) {
                                    $imgUrl = asset($imgPath);         // ya viene 'storage/...'
                                } else {
                                    $imgUrl = asset('storage/'.$imgPath); // viene 'rewards/...'
                                }
                            } else {
                                $imgUrl = asset('img/placeholder-reward.png');
                            }

                            $isNew = isset($reward->created_at) && now()->diffInDays($reward->created_at) <= 14;
                        @endphp

                        <article class="card">
                            <div class="media">
                                <img src="{{ $imgUrl }}" alt="{{ $reward->name }}" style="width:100%;height:100%;object-fit:cover" loading="lazy" onerror="this.src='{{ asset('img/placeholder-reward.png') }}'">
                                @if($isNew) <span class="chip badge">Nuevo</span> @endif
                            </div>

                            <div class="body">
                                <div style="flex:1">
                                    <div style="font-weight:700">{{ $reward->name }}</div>
                                    <div class="meta">{{ $reward->commerce->name ?? 'Comercio aliado' }} · {{ $reward->category->name ?? 'General' }}</div>
                                </div>
                                <span class="tag">{{ $reward->points_required }} pts</span>
                            </div>

                            <div class="footer">
                                <button
                                    class="btn"
                                    data-open-details
                                    data-id="{{ $reward->id }}"
                                    data-title="{{ $reward->name }}"
                                    data-cost="{{ $reward->points_required }}"
                                    data-img="{{ $imgUrl }}"
                                    data-vendor="{{ $reward->commerce->name ?? 'Comercio aliado' }}"
                                    data-desc="{{ e($reward->description ?? '') }}"
                                >Detalles</button>

                                <form method="POST" action="{{ route('donor.rewards.redeem', $reward->id) }}" class="js-redeem-form">
                                    @csrf
                                    @php $can = ($totalPoints ?? 0) >= (int) $reward->points_required; @endphp
                                    <button class="btn primary" type="submit" data-cost="{{ $reward->points_required }}" {{ $can ? '' : 'disabled' }}>
                                        Canjear
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="empty">Aún no hay recompensas disponibles.</div>
                    @endforelse
                </div>
            </div>

            {{-- Cupones --}}
            <div data-tab-content="cupones" style="display:none">
                @if(empty($ownedCoupons) || count($ownedCoupons)===0)
                    <div class="empty">No tienes cupones activos todavía.</div>
                @else
                    <div class="grid">
                        @foreach($ownedCoupons as $cp)
                            @php
                                if (!empty($cp->image_path)) {
                                    if (Str::startsWith($cp->image_path, ['http://','https://'])) {
                                        $cpImg = $cp->image_path;
                                    } elseif (Str::contains($cp->image_path, 'storage/')) {
                                        $cpImg = asset($cp->image_path);
                                    } else {
                                        $cpImg = asset('storage/'.$cp->image_path);
                                    }
                                } else {
                                    $cpImg = null;
                                }
                            @endphp
                            <article class="card">
                                <div class="media">
                                    @if ($cpImg)
                                        <img src="{{ $cpImg }}" alt="{{ $cp->title }}" style="width:100%;height:100%;object-fit:cover" loading="lazy" onerror="this.src='{{ asset('img/placeholder-reward.png') }}'">
                                    @endif
                                    <span class="chip badge">Activo</span>
                                </div>
                                <div class="body">
                                    <div style="flex:1">
                                        <div style="font-weight:700">{{ $cp->title }}</div>
                                        <div class="meta">Código: <b>{{ $cp->code }}</b> · Vence: {{ optional($cp->expires_at)->format('d/m/Y') ?? '—' }}</div>
                                    </div>
                                    <span class="tag">{{ $cp->vendor_name ?? 'Comercio' }}</span>
                                </div>
                                <div class="footer">
                                    <button class="btn js-copy-code" data-code="{{ $cp->code }}">Copiar código</button>
                                    <a class="btn ghost" href="{{ $cp->redeem_url ?? '#' }}" target="_blank" rel="noopener">Usar</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Actividad --}}
            <div data-tab-content="actividad" style="display:none">
                <div class="panel">
                    <div class="timeline">
                        @forelse(($activities ?? []) as $ev)
                            <div class="tl-item">
                                <div class="dot"></div>
                                <div>
                                    <div style="font-weight:600">{{ $ev['title'] ?? 'Actividad' }}</div>
                                    <div class="sub">{{ $ev['desc'] ?? '' }}</div>
                                    <div class="meta">{{ \Carbon\Carbon::parse($ev['date'] ?? now())->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty">Sin actividad reciente.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

{{-- Modal Detalles --}}
<div id="ds-details-modal" class="modal" aria-hidden="true">
  <div class="box panel">
    <div class="row" style="justify-content:space-between">
      <h2 data-ds="title">Recompensa</h2>
      <button class="btn ghost js-modal-close" aria-label="Cerrar">Cerrar</button>
    </div>
    <img data-ds="img" style="display:none;max-width:100%;border-radius:12px;margin-top:8px" alt="Imagen de recompensa">
    <div class="sub" style="margin-top:8px">Comercio: <b data-ds="vendor"></b></div>
    <div data-ds="desc" style="margin-top:8px"></div>
    <div class="row" style="justify-content:space-between;margin-top:12px">
      <span class="tag"><span data-ds="cost"></span></span>

      {{-- El JS reemplaza __ID__ por el id real --}}
      <form class="js-redeem-form" method="POST"
            data-redeem-template="{{ url('/donor/rewards/__ID__/redeem') }}">
        @csrf
        <button class="btn primary" type="submit">Canjear</button>
      </form>
    </div>
  </div>
</div>

{{-- Modal Confirmación --}}
<div id="ds-confirm-modal" class="modal" aria-hidden="true">
  <div class="box panel">
    <h2>Confirmar canje</h2>
    <p>Se descontarán <b id="ds-confirm-cost"></b> puntos. ¿Deseás continuar?</p>
    <div class="row" style="justify-content:flex-end">
      <button class="btn" data-confirm-no>Cancelar</button>
      <button class="btn primary" data-confirm-yes>Confirmar</button>
    </div>
  </div>
</div>
@endsection
