<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\RewardStore;
use App\Models\RewardCategory;
use App\Models\Commerce;
use App\Models\Image;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $rewards = RewardStore::with(['category', 'commerce', 'images'])
            ->when($request->filled('category'), fn($q) => $q->where('reward_category_id', $request->category))
            ->when($request->filled('commerce'), fn($q) => $q->where('commerce_id', $request->commerce))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            // Historial / archivadas (soft delete)
            ->when($request->boolean('with_trashed'), fn($q) => $q->withTrashed())
            ->when($request->boolean('only_trashed'), fn($q) => $q->onlyTrashed())
            ->latest()
            ->paginate(20);

        $categories = RewardCategory::all();
        $commerces  = Commerce::all();

        return view('admin.modules.rewards.index', compact('rewards','categories','commerces'));
    }

    public function create()
    {
        $categories = RewardCategory::all();
        $commerces  = Commerce::all();
        return view('admin.modules.rewards.create', compact('categories', 'commerces'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string',
            'reward_category_id' => 'required|exists:reward_categories,id',
            'commerce_id'        => 'nullable|uuid|exists:commerce,id',
            'points_required'    => 'required|integer|min:1',
            'stock'              => 'required|integer|min:0',
            'is_monthly_promo'   => 'boolean',
            'image'              => 'nullable',
            'image.*'            => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($data, $request) {
            $reward = RewardStore::create([
                'id'                 => (string) Str::uuid(),
                'name'               => $data['name'],
                'description'        => $data['description'] ?? null,
                'reward_category_id' => $data['reward_category_id'],
                'commerce_id'        => $data['commerce_id'] ?? null,
                'points_required'    => $data['points_required'],
                'stock'              => $data['stock'],
                'is_monthly_promo'   => (bool) ($data['is_monthly_promo'] ?? false),
            ]);

            $this->storeImages($request, $reward->id);

            // Si se crea ya sin stock, archivar de una
            if ($reward->stock <= 0 && !$reward->trashed()) {
                $reward->delete();
            }
        });

        return redirect()->route('admin.rewards.index')->with('success', 'Recompensa creada exitosamente.');
    }

    public function edit($id)
    {
        $reward     = RewardStore::with('images')->findOrFail($id);
        $categories = RewardCategory::all();
        $commerces  = Commerce::all();
        return view('admin.modules.rewards.edit', compact('reward', 'categories', 'commerces'));
    }

    public function update(Request $request, $id)
    {
        $reward = RewardStore::withTrashed()->findOrFail($id);

        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string',
            'reward_category_id' => 'required|exists:reward_categories,id',
            'commerce_id'        => 'nullable|uuid|exists:commerce,id',
            'points_required'    => 'required|integer|min:1',
            'stock'              => 'required|integer|min:0',
            'is_monthly_promo'   => 'boolean',
            'image'              => 'nullable',
            'image.*'            => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($reward, $data, $request) {
            $reward->update([
                'name'               => $data['name'],
                'description'        => $data['description'] ?? null,
                'reward_category_id' => $data['reward_category_id'],
                'commerce_id'        => $data['commerce_id'] ?? null,
                'points_required'    => $data['points_required'],
                'stock'              => $data['stock'],
                'is_monthly_promo'   => (bool) ($data['is_monthly_promo'] ?? false),
            ]);

            $this->storeImages($request, $reward->id);

            // Auto-archivar/restaurar según stock
            if ($reward->stock <= 0 && !$reward->trashed()) {
                $reward->delete(); // archiva
            } elseif ($reward->stock > 0 && $reward->trashed()) {
                $reward->restore(); // vuelve a estar activa
            }
        });

        return redirect()->route('admin.rewards.index')->with('success', 'Recompensa actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $reward = RewardStore::withTrashed()->with('images')->findOrFail($id);

        DB::transaction(function () use ($reward) {
            // Si tiene canjes, archivar (soft delete) y listo
            $hasRedemptions = \App\Models\UserReward::where('reward_id', $reward->id)->exists();
            if ($hasRedemptions) {
                if (!$reward->trashed()) {
                    $reward->delete();
                }
                return;
            }

            // Sin canjes: borrado definitivo y limpiar imágenes
            foreach ($reward->images as $img) {
                if ($img->path && Storage::disk('public')->exists($img->path)) {
                    Storage::disk('public')->delete($img->path);
                }
                $img->delete();
            }

            // Si estaba archivada, forzar delete; si no, también
            $reward->forceDelete();
        });

        return redirect()->route('admin.rewards.index')->with('success', 'Recompensa eliminada.');
    }

    public function show($id)
    {
        $reward = RewardStore::withTrashed()->with(['category', 'commerce', 'images'])->findOrFail($id);
        return view('admin.modules.rewards.show', compact('reward'));
    }

    // (Opcional) Restaurar desde historial
    public function restore($id)
    {
        $reward = RewardStore::withTrashed()->findOrFail($id);
        DB::transaction(function () use ($reward) {
            // Si no tiene stock, restaurar no tiene sentido visualmente,
            // pero permitimos restaurar por gestión; admin decide.
            $reward->restore();
        });

        return redirect()->route('admin.rewards.index')->with('success', 'Recompensa restaurada.');
    }

    private function storeImages(Request $request, string $rewardId): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $files = $request->file('image');
        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            if (!$file) continue;

            $stored = $file->store('rewards', 'public');

            Image::create([
                'id'        => (string) Str::uuid(),
                'reward_id' => $rewardId,
                'path'      => $stored,
                'type'      => 'promotion',
            ]);
        }
    }
}
