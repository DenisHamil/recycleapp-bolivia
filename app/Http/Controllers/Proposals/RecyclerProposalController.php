<?php

namespace App\Http\Controllers\Proposals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\RecyclerProposal;
use App\Models\Notification;
use App\Models\Donation;
use App\Models\UserPoint;

class RecyclerProposalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'donation_id'   => 'required|exists:donations,id',
            'proposed_date' => 'nullable|date',
            'proposed_time' => 'nullable|date_format:H:i',
        ]);

        $donation = Donation::with('category')->findOrFail($request->donation_id);

        if (!in_array($donation->state, ['pending', 'available', 'open'])) {
            return back()->with('error', 'Esta donación no está disponible para recibir propuestas.');
        }

        $exists = RecyclerProposal::where('donation_id', $donation->id)
            ->where('collector_id', Auth::id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ya enviaste una propuesta para esta donación.');
        }

        DB::beginTransaction();

        try {
            $proposalId = (string) Str::uuid();

            RecyclerProposal::create([
                'id'            => $proposalId,
                'donation_id'   => $donation->id,
                'collector_id'  => Auth::id(),
                'proposed_date' => $request->proposed_date,
                'proposed_time' => $request->proposed_time,
                'status'        => 'waiting',
                'created_at'    => now(),
            ]);

            Notification::create([
                'id'         => (string) Str::uuid(),
                'user_id'    => $donation->donor_id,
                'title'      => '📩 Nueva propuesta de recolección',
                'message'    => 'Has recibido una propuesta para tu donación de "' . ($donation->category->name ?? 'Sin categoría') . '".',
                'type'       => 'proposal.received',
                'related_id' => $proposalId,
                'is_read'    => false,
                'created_at' => now(),
            ]);

            DB::table('activity_log')->insert([
                [
                    'id'              => (string) Str::uuid(),
                    'user_id'         => Auth::id(),
                    'action_type'     => 'proposal.sent',
                    'reference_table' => 'donations',
                    'reference_id'    => $donation->id,
                    'detail'          => 'Propuesta enviada',
                    'created_at'      => now(),
                ],
                [
                    'id'              => (string) Str::uuid(),
                    'user_id'         => $donation->donor_id,
                    'action_type'     => 'proposal.received',
                    'reference_table' => 'donations',
                    'reference_id'    => $donation->id,
                    'detail'          => 'Propuesta recibida',
                    'created_at'      => now(),
                ],
            ]);

            DB::commit();
            return back()->with('success', '✅ Propuesta enviada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('🚨 Error al crear propuesta', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al enviar la propuesta: ' . $e->getMessage());
        }
    }

    public function accept($id)
    {
        $proposal = RecyclerProposal::with('collector')->findOrFail($id);

        DB::beginTransaction();

        try {
            $donation = Donation::with('category', 'donor')
                ->where('id', $proposal->donation_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($donation->donor_id !== Auth::id()) {
                abort(403, 'No estás autorizado.');
            }

            if (in_array($donation->state, ['completed', 'cancelled'], true)) {
                return back()->with('error', 'Donación ya finalizada o cancelada.');
            }

            if ($donation->state === 'accepted' && $donation->collector_id !== $proposal->collector_id) {
                return back()->with('error', 'Ya fue aceptada por otro recolector.');
            }

            $proposal->status = 'accepted';
            $proposal->save();

            RecyclerProposal::where('donation_id', $donation->id)
                ->where('id', '!=', $proposal->id)
                ->update(['status' => 'rejected']);

            $donation->collector_id = $proposal->collector_id;
            $donation->state = 'accepted';
            $donation->confirmed_by_collector = true;
            $donation->save();

            Notification::create([
                'id'         => (string) Str::uuid(),
                'user_id'    => $proposal->collector_id,
                'title'      => '✅ Tu propuesta fue aceptada',
                'message'    => 'Has sido asignado para recoger la donación de "' . ($donation->category->name ?? 'residuo') . '".',
                'type'       => 'proposal.accepted',
                'related_id' => $proposal->id,
                'is_read'    => false,
                'created_at' => now(),
            ]);

            DB::table('activity_log')->insert([
                [
                    'id'              => (string) Str::uuid(),
                    'user_id'         => $donation->donor_id,
                    'action_type'     => 'donation.assigned',
                    'reference_table' => 'donations',
                    'reference_id'    => $donation->id,
                    'detail'          => 'Aceptaste una propuesta',
                    'created_at'      => now(),
                ],
                [
                    'id'              => (string) Str::uuid(),
                    'user_id'         => $proposal->collector_id,
                    'action_type'     => 'collection.assigned',
                    'reference_table' => 'donations',
                    'reference_id'    => $donation->id,
                    'detail'          => 'Asignado como recolector',
                    'created_at'      => now(),
                ],
            ]);

            DB::commit();
            return back()->with('success', '✅ Propuesta aceptada.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('🚨 Error al aceptar propuesta', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al aceptar la propuesta: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, $proposalId)
    {
        Log::info("🟡 Iniciando finalización de recolección para propuesta: $proposalId por usuario: " . Auth::id());

        $request->validate([
            'confirmed_weight' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $proposal = RecyclerProposal::with(['donation.category', 'donation.donor'])->findOrFail($proposalId);
            $donation = $proposal->donation;

            if ($donation->state !== 'accepted') {
                abort(403, 'La donación no está en estado aceptado.');
            }

            if ($donation->collector_id !== Auth::id()) {
                abort(403, 'No estás autorizado para finalizar esta recolección.');
            }

            $donation->confirmed_weight = $request->confirmed_weight;
            $donation->state = 'completed';
            $donation->finalized_at = now();
            $donation->save();

            $points = $donation->confirmed_weight * ($donation->category->points_per_kilo ?? 1);

            UserPoint::create([
                'id'          => (string) Str::uuid(),
                'user_id'     => $donation->donor_id,
                'points'      => $points,
                'action'      => 'donation_completed',
                'description' => 'Tu donación fue completada. ID: ' . $donation->id,
                'created_at'  => now(),
            ]);

            Notification::insert([
                [
                    'id'         => (string) Str::uuid(),
                    'user_id'    => $donation->collector_id,
                    'title'      => '🎉 Recolección finalizada',
                    'message'    => 'Finalizaste la recolección de "' . ($donation->category->name ?? 'residuo') . '".',
                    'type'       => 'donation.completed',
                    'related_id' => $donation->id,
                    'is_read'    => false,
                    'created_at' => now(),
                ],
                [
                    'id'         => (string) Str::uuid(),
                    'user_id'    => $donation->donor_id,
                    'title'      => '✅ Tu donación fue recogida',
                    'message'    => 'Tu donación fue recogida y completada. Ganaste ' . $points . ' puntos.',
                    'type'       => 'donation.completed',
                    'related_id' => $donation->id,
                    'is_read'    => false,
                    'created_at' => now(),
                ],
                [
                    'id'         => (string) Str::uuid(),
                    'user_id'    => $donation->collector_id,
                    'title'      => '⭐ Califica al donador',
                    'message'    => '¿Cómo fue tu experiencia con el donador? ¡Deja una calificación!',
                    'type'       => 'rating.request',
                    'related_id' => $proposalId,
                    'is_read'    => false,
                    'created_at' => now(),
                ],
                [
                    'id'         => (string) Str::uuid(),
                    'user_id'    => $donation->donor_id,
                    'title'      => '⭐ Califica al recolector',
                    'message'    => '¿Cómo fue tu experiencia con el recolector? ¡Deja una calificación!',
                    'type'       => 'rating.request',
                    'related_id' => $proposalId,
                    'is_read'    => false,
                    'created_at' => now(),
                ],
            ]);

            DB::table('activity_log')->insert([
                [
                    'id'              => (string) Str::uuid(),
                    'user_id'         => Auth::id(),
                    'action_type'     => 'donation.completed',
                    'reference_table' => 'donations',
                    'reference_id'    => $donation->id,
                    'detail'          => 'Recolección completada con ' . $donation->confirmed_weight . ' kg',
                    'created_at'      => now(),
                ],
            ]);

            DB::commit();

            return back()->with('success', '✅ Recolección finalizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('❌ Error al finalizar recolección', [
                'error' => $e->getMessage(),
                'proposal_id' => $proposalId,
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Error al finalizar: ' . $e->getMessage());
        }
    }
}
