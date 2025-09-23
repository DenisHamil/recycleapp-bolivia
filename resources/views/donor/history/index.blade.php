@extends('donor.layouts.app')

@section('title', 'Historial')

@section('content')
<style>
  .card { background:#fff; border-radius:16px; padding:16px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
  .table { width:100%; border-collapse:separate; border-spacing:0; }
  .table th, .table td { padding:12px 10px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
  .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; line-height:1; }
  .badge-gray { background:#e5e7eb; color:#111827; }
  .badge-green { background:#dcfce7; color:#065f46; }
  .badge-amber { background:#fef3c7; color:#7c2d12; }
  .badge-red { background:#fee2e2; color:#7f1d1d; }
  .btn { display:inline-flex; align-items:center; gap:.25rem; padding:.45rem .7rem; border-radius:10px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; text-decoration:none; }
  .btn:hover { background:#f9fafb; }
  .btn-danger { border-color:#dc2626; color:#dc2626; }
  h1 { font-size:1.4rem; margin:0 0 10px; }
</style>

<div class="card">
  <h1>Historial de Donaciones</h1>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Material</th>
          <th>Estado</th>
          <th>Detalle</th>
          <th>Fecha</th>
          <th style="width:1%;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          @php
            $donation = null;
            if (($log->reference_table ?? null) === 'donations' && isset($donationsById[$log->reference_id])) {
                $donation = $donationsById[$log->reference_id];
            }

            $tipo = $donation?->category?->name ?? 'Donación';
            $state = $donation?->state;
            $badgeClass = match($state) {
              'completed' => 'badge-green',
              'accepted'  => 'badge-amber',
              'pending'   => 'badge-gray',
              'cancelled' => 'badge-red',
              default     => 'badge-gray',
            };

            $hasCollector = !empty($donation?->collector_id);
            $canCancel = $donation && $hasCollector && $donation->state === 'accepted';
          @endphp

          <tr>
            <td><span class="badge badge-gray">{{ $tipo }}</span></td>
            <td>
              @if($donation)
                <span class="badge {{ $badgeClass }}">
                  {{ ucfirst($donation->state ?? '—') }}
                </span>
              @else
                <span class="badge badge-gray">—</span>
              @endif
            </td>
            <td>{{ $log->detail_formatted ?? '—' }}</td>
            <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
            <td>
              @if($canCancel)
                <button class="btn btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#cancelModal"
                        data-donation="{{ $donation->id }}">
                  Cancelar
                </button>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted">Sin movimientos por ahora.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-2">
    {{ $logs->links() }}
  </div>
</div>

{{-- Modal cancelar --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="cancelForm" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Cancelar donación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Motivo *</label>
        <textarea name="reason" class="form-control" rows="3" required placeholder="Explica brevemente el motivo de la cancelación..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-danger">Cancelar donación</button>
      </div>
    </form>
  </div>
</div>

<script>
  const cancelModal = document.getElementById('cancelModal');
  const cancelForm  = document.getElementById('cancelForm');

  cancelModal?.addEventListener('show.bs.modal', (ev) => {
    const btn = ev.relatedTarget;
    const donationId = btn?.getAttribute('data-donation');
    if (!donationId) return;
    cancelForm.action = "{{ route('donor.donations.cancel', ':id') }}".replace(':id', donationId);
    cancelForm.reset();
  });
</script>
@endsection
