@extends('admin.layout')
@section('title', 'Nueva recompensa')

@section('content')
<div class="container">
  <h1>Nueva recompensa</h1>

  @if ($errors->any())
    <div class="panel danger">
      <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="panel">
    <form method="POST" action="{{ route('admin.rewards.store') }}" enctype="multipart/form-data" class="form-grid">
      @csrf

      <div class="full">
        <label>Nombre *</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
      </div>

      <div class="full">
        <label>Descripción</label>
        <textarea name="description" rows="4">{{ old('description') }}</textarea>
      </div>

      <div>
        <label class="flex-between">
          <span>Categoría *</span>
          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCategory">+ Nueva categoría</button>
        </label>
        <select name="reward_category_id" id="selectCategory" required>
          <option value="">Seleccionar…</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('reward_category_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="flex-between">
          <span>Comercio</span>
          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCommerce">+ Nuevo comercio</button>
        </label>
        <select name="commerce_id" id="selectCommerce">
          <option value="">— Ninguno —</option>
          @foreach($commerces as $v)
            <option value="{{ $v->id }}" @selected(old('commerce_id')==$v->id)>{{ $v->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label>Puntos requeridos *</label>
        <input type="number" min="1" name="points_required" value="{{ old('points_required') }}" required>
      </div>

      <div>
        <label>Stock *</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required>
      </div>

      <div class="full">
        <label><input type="checkbox" name="is_monthly_promo" value="1" @checked(old('is_monthly_promo'))> ¿Promoción mensual?</label>
      </div>

      <div class="full">
        <label>Imágenes (arrastrá o seleccioná varias)</label>
        <div id="ar-drop" class="drop">Soltá aquí las imágenes o hacé click abajo</div>
        <input type="file" name="image[]" multiple accept="image/*">
        <div id="ar-preview" class="gallery" style="margin-top:8px"></div>
        <div class="sub">Podés quitar una previsualización antes de guardar.</div>
      </div>

      <div class="full actions">
        <button class="btn primary" type="submit">Guardar</button>
        <a class="btn" href="{{ route('admin.rewards.index') }}">Cancelar</a>
      </div>
    </form>
  </div>
</div>

{{-- ========= Modal Nueva Categoría (Bootstrap) ========= --}}
<div class="modal fade" id="modalCategory" tabindex="-1" aria-labelledby="modalCategoryLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formNewCategory" action="{{ route('admin.reward-categories.store') }}" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCategoryLabel">Nueva categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Nombre *</label>
        <input type="text" name="name" class="form-control" required>

        <label class="form-label mt-2">Icono (opcional)</label>
        <input type="file" name="icon" class="form-control" accept="image/*">

        <div class="text-danger small mt-2" id="catErrors" hidden></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnCatSave">Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- ========= Modal Nuevo Comercio (Bootstrap) ========= --}}
<div class="modal fade" id="modalCommerce" tabindex="-1" aria-labelledby="modalCommerceLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formNewCommerce" action="{{ route('admin.commerces.store') }}" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCommerceLabel">Nuevo comercio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Nombre *</label>
        <input type="text" name="name" class="form-control" required>

        <label class="form-label mt-2">Descripción</label>
        <textarea name="description" rows="3" class="form-control"></textarea>

        <label class="form-label mt-2">Logo (opcional)</label>
        <input type="file" name="logo" class="form-control" accept="image/*">

        <div class="text-danger small mt-2" id="comErrors" hidden></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnComSave">Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- ====== estilos mínimos adicionales ====== --}}
<style>
  .flex-between{display:flex;justify-content:space-between;align-items:center}
</style>

{{-- ====== JS AJAX para crear y actualizar selects ====== --}}
<script>
  const CSRF = '{{ csrf_token() }}';

  // Mapa de locks y AbortControllers por formulario (id)
  window._formLocks = window._formLocks || {};
  window._formControllers = window._formControllers || {};
  window._formLastSubmitAt = window._formLastSubmitAt || {};

  function hideBsModal(modalEl) {
    const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    inst.hide();
  }

  async function submitMiniForm(formEl, errorsBoxId, selectElId) {
    // ⛔ Anti-doble click/enter: lock + debounce 1.5s
    const fid = formEl.id || Math.random().toString(36).slice(2);
    const now = Date.now();
    const last = window._formLastSubmitAt[fid] || 0;
    if (window._formLocks[fid] || (now - last) < 1500) return;
    window._formLocks[fid] = true;
    window._formLastSubmitAt[fid] = now;

    // Cancela un intento previo en curso, por si acaso
    if (window._formControllers[fid]) {
      try { window._formControllers[fid].abort(); } catch(_) {}
    }
    const controller = new AbortController();
    window._formControllers[fid] = controller;

    const btn = formEl.querySelector('button[type="submit"]');
    const errorsBox = document.getElementById(errorsBoxId);
    if (errorsBox) { errorsBox.hidden = true; errorsBox.textContent = ''; }
    if (btn) { btn.disabled = true; btn.dataset._originalText = btn.textContent; btn.textContent = 'Guardando…'; }
    formEl.style.pointerEvents = 'none'; // bloquea clicks en todo el form

    try {
      // Idempotencia suave: adjunta una clave única por intento
      const idem = crypto?.randomUUID ? crypto.randomUUID() : (Math.random().toString(36).slice(2) + Date.now());
      const fd = new FormData(formEl);
      fd.append('idempotency_key', idem);

      const res = await fetch(formEl.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: fd,
        signal: controller.signal
      });

      if (res.status === 422) {
        const data = await res.json();
        const errs = Object.values(data.errors || {}).flat();
        if (errorsBox) {
          errorsBox.innerHTML = errs.map(e=>`<div>• ${e}</div>`).join('');
          errorsBox.hidden = false;
        }
        return;
      }

      // Si el backend devuelve JSON con {id, name}, actualizamos el select
      let data = null;
      try { data = await res.json(); } catch(_) {}

      if (data && data.id && data.name) {
        const select = document.getElementById(selectElId);
        if (select) {
          // Evita duplicar la opción si ya existe
          let exists = Array.from(select.options).some(o => String(o.value) === String(data.id));
          if (!exists) {
            const opt = document.createElement('option');
            opt.value = data.id; opt.textContent = data.name;
            select.appendChild(opt);
          }
          select.value = data.id;
        }
        const modalEl = formEl.closest('.modal');
        if (modalEl) hideBsModal(modalEl);
        formEl.reset();
        return;
      }

      // Si no hubo JSON (por ejemplo, redirección normal), recargamos
      location.reload();

    } catch (e) {
      if (errorsBox) {
        errorsBox.textContent = 'Ocurrió un error. Intentá nuevamente.';
        errorsBox.hidden = false;
      }
    } finally {
      // Liberamos UI pero mantenemos la protección de re-click por 1.5s (debounce ya aplicado arriba)
      if (btn) { btn.disabled = false; btn.textContent = btn.dataset._originalText || 'Guardar'; }
      formEl.style.pointerEvents = '';
      window._formLocks[fid] = false;
      // No reutilizamos el controller
      delete window._formControllers[fid];
    }
  }

  // Evita submit doble por Enter + Click: listener de submit único
  const catForm = document.getElementById('formNewCategory');
  const comForm = document.getElementById('formNewCommerce');

  catForm?.addEventListener('submit', function(e){
    e.preventDefault();
    submitMiniForm(this, 'catErrors', 'selectCategory');
  }, { passive:false });

  comForm?.addEventListener('submit', function(e){
    e.preventDefault();
    submitMiniForm(this, 'comErrors', 'selectCommerce');
  }, { passive:false });

</script>

@endsection
