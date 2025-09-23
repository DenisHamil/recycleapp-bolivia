<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RewardCategoryController extends Controller
{
    public function index()
    {
        $categories = RewardCategory::orderBy('name')->paginate(20);
        return view('admin.modules.reward_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.modules.reward_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|image|max:2048',
        ]);

        $normalizedName = preg_replace('/\s+/u', ' ', trim($data['name']));

        // Si ya existe (ignorando mayúsculas/minúsculas), devolver ese
        $existing = RewardCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])->first();
        if ($existing) {
            if ($request->ajax()) {
                return response()->json(['id' => $existing->id, 'name' => $existing->name]);
            }
            return redirect()->route('admin.reward-categories.index')
                ->with('success', 'Ya existía una categoría con ese nombre. Se usó la existente.');
        }

        $category = DB::transaction(function () use ($data, $request, $normalizedName) {
            $iconPath = null;

            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $dir  = 'uploads/reward_categories';
                if (!is_dir(public_path($dir))) {
                    @mkdir(public_path($dir), 0775, true);
                }
                $name = uniqid() . '_' . strtolower(str_replace(' ', '_', $file->getClientOriginalName()));
                $file->move(public_path($dir), $name);
                $iconPath = $dir . '/' . $name;
            }

            return RewardCategory::create([
                'name'      => $normalizedName,
                'icon_path' => $iconPath,
            ]);
        });

        if ($request->ajax()) {
            return response()->json(['id' => $category->id, 'name' => $category->name]);
        }

        return redirect()->route('admin.reward-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(RewardCategory $reward_category)
    {
        return view('admin.modules.reward_categories.edit', [
            'category' => $reward_category,
        ]);
    }

    public function update(Request $request, RewardCategory $reward_category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|image|max:2048',
        ]);

        $normalizedName = preg_replace('/\s+/u', ' ', trim($data['name']));

        // Evitar colisión de nombre con otro registro
        $existsOther = RewardCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
            ->where('id', '!=', $reward_category->id)
            ->exists();

        if ($existsOther) {
            return back()
                ->withErrors(['name' => 'Ya existe otra categoría con ese nombre.'])
                ->withInput();
        }

        DB::transaction(function () use ($data, $request, $reward_category, $normalizedName) {
            if ($request->hasFile('icon')) {
                if ($reward_category->icon_path && file_exists(public_path($reward_category->icon_path))) {
                    @unlink(public_path($reward_category->icon_path));
                }
                $file = $request->file('icon');
                $dir  = 'uploads/reward_categories';
                if (!is_dir(public_path($dir))) {
                    @mkdir(public_path($dir), 0775, true);
                }
                $name = uniqid() . '_' . strtolower(str_replace(' ', '_', $file->getClientOriginalName()));
                $file->move(public_path($dir), $name);
                $reward_category->icon_path = $dir . '/' . $name;
            }

            $reward_category->name = $normalizedName;
            $reward_category->save();
        });

        return redirect()->route('admin.reward-categories.index')
            ->with('success', 'Categoría actualizada.');
    }

    public function destroy(RewardCategory $reward_category)
    {
        DB::transaction(function () use ($reward_category) {
            if ($reward_category->icon_path && file_exists(public_path($reward_category->icon_path))) {
                @unlink(public_path($reward_category->icon_path));
            }
            $reward_category->delete();
        });

        return redirect()->route('admin.reward-categories.index')
            ->with('success', 'Categoría eliminada.');
    }
}