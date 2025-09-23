<?php

namespace App\Http\Controllers\Collector;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Donation;
use App\Models\RecyclerProposal;

class CollectorDonationMapController extends Controller
{
    /**
     * Muestra la vista del mapa interactivo con donaciones
     */
    public function index(): View
    {
        return view('collector.donations-map');
    }

    /**
     * Devuelve las donaciones en formato JSON filtradas según la especialización del recolector
     */
    public function mapData(): JsonResponse
    {
        /** @var \App\Models\User $collector */
        $collector = Auth::user();

        // Seguridad: debe estar autenticado y tener rol "collector"
        if (!$collector || $collector->role !== 'collector') {
            return response()->json(['error' => 'Acceso no autorizado.'], 403);
        }

        // Cargar especializaciones
        $collector->loadMissing('collectorSpecializations');
        $allowedCategoryIds = $collector->collectorSpecializations->pluck('category_id')->toArray();

        // Obtener donaciones con relaciones necesarias
        $donations = Donation::with(['category', 'donor', 'images'])
            ->where(function ($query) use ($collector, $allowedCategoryIds) {
                $query->where(function ($q) use ($allowedCategoryIds) {
                    // Mostrar donaciones no asignadas dentro de la especialización del recolector
                    $q->whereNull('collector_id')
                      ->whereIn('category_id', $allowedCategoryIds);
                })->orWhere(function ($q) use ($collector) {
                    // Mostrar donaciones que ya fueron asignadas a este recolector
                    $q->where('collector_id', $collector->id);
                });
            })
            ->get()
            ->map(function ($donation) use ($collector) {
                // Buscar la propuesta aceptada si este recolector fue asignado
                $proposal = RecyclerProposal::where('donation_id', $donation->id)
                    ->where('collector_id', $collector->id)
                    ->where('status', 'accepted')
                    ->first();

                return [
                    'id' => $donation->id,
                    'latitude' => $donation->latitude,
                    'longitude' => $donation->longitude,
                    'category_name' => $donation->category->name ?? 'Sin categoría',
                    'category_color' => $donation->category->color ?? '#888',
                    'description' => $donation->description,
                    'weight' => $donation->estimated_weight ?? 'N/D',
                    'donor_name' => $donation->donor->first_name . ' ' . $donation->donor->last_name,
                    'address' => $donation->address_description ?? 'No especificada',
                    'available_from_date' => $donation->available_from_date,
                    'available_until_date' => $donation->available_until_date,
                    'available_from_time' => $donation->available_from_time,
                    'available_until_time' => $donation->available_until_time,
                    'image_path' => $donation->images->first()?->path ? asset($donation->images->first()->path) : null,
                    'state' => $donation->state,
                    'collector_id' => $donation->collector_id,
                    'proposal_id' => $proposal?->id,
                ];
            });

        return response()->json($donations);
    }
}
