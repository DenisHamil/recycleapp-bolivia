<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commerce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommerceController extends Controller
{
    public function index()
    {
        $commerces = Commerce::orderBy('name')->paginate(20);
        return view('admin.modules.commerces.index', compact('commerces'));
    }

    public function create()
    {
        return view('admin.modules.commerces.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|max:2048',
        ]);

        // Normalizar nombre para evitar duplicados por espacios/casings
        $normalizedName = preg_replace('/\s+/u', ' ', trim($data['name']));

        // Si ya existe (ignorando mayúsculas/minúsculas), devolvemos ese
        $existing = Commerce::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])->first();
        if ($existing) {
            if ($request->ajax()) {
                return response()->json(['id' => $existing->id, 'name' => $existing->name]);
            }
            return redirect()->route('admin.commerces.index')
                ->with('success', 'Ya existía un comercio con ese nombre. Se usó el existente.');
        }

        $commerce = DB::transaction(function () use ($data, $request, $normalizedName) {
            $logoPath = null;

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $dir  = 'uploads/commerces';
                if (!is_dir(public_path($dir))) {
                    @mkdir(public_path($dir), 0775, true);
                }
                $name = uniqid() . '_' . strtolower(str_replace(' ', '_', $file->getClientOriginalName()));
                $file->move(public_path($dir), $name);
                $logoPath = $dir . '/' . $name;
            }

            return Commerce::create([
                'name'        => $normalizedName,
                'description' => $data['description'] ?? null,
                'logo_path'   => $logoPath,
            ]);
        });

        if ($request->ajax()) {
            return response()->json(['id' => $commerce->id, 'name' => $commerce->name]);
        }

        return redirect()->route('admin.commerces.index')
            ->with('success', 'Comercio creado correctamente.');
    }

    public function edit(Commerce $commerce)
    {
        return view('admin.modules.commerces.edit', compact('commerce'));
    }

    public function update(Request $request, Commerce $commerce)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|max:2048',
        ]);

        $normalizedName = preg_replace('/\s+/u', ' ', trim($data['name']));

        // Evitar colisión de nombre con otro registro
        $existsOther = Commerce::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
            ->where('id', '!=', $commerce->id)
            ->exists();

        if ($existsOther) {
            return back()
                ->withErrors(['name' => 'Ya existe otro comercio con ese nombre.'])
                ->withInput();
        }

        DB::transaction(function () use ($data, $request, $commerce, $normalizedName) {
            if ($request->hasFile('logo')) {
                if ($commerce->logo_path && file_exists(public_path($commerce->logo_path))) {
                    @unlink(public_path($commerce->logo_path));
                }
                $file = $request->file('logo');
                $dir  = 'uploads/commerces';
                if (!is_dir(public_path($dir))) {
                    @mkdir(public_path($dir), 0775, true);
                }
                $name = uniqid() . '_' . strtolower(str_replace(' ', '_', $file->getClientOriginalName()));
                $file->move(public_path($dir), $name);
                $commerce->logo_path = $dir . '/' . $name;
            }

            $commerce->name        = $normalizedName;
            $commerce->description = $data['description'] ?? null;
            $commerce->save();
        });

        return redirect()->route('admin.commerces.index')
            ->with('success', 'Comercio actualizado.');
    }

    public function destroy(Commerce $commerce)
    {
        DB::transaction(function () use ($commerce) {
            if ($commerce->logo_path && file_exists(public_path($commerce->logo_path))) {
                @unlink(public_path($commerce->logo_path));
            }
            $commerce->delete();
        });

        return redirect()->route('admin.commerces.index')
            ->with('success', 'Comercio eliminado.');
    }
}