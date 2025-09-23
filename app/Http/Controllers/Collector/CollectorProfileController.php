<?php

namespace App\Http\Controllers\Collector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;

class CollectorProfileController extends Controller
{
    /**
     * Muestra el formulario para editar el perfil del recolector.
     */
    public function edit()
    {
        /** @var User $collector */
        $collector = Auth::user();

        if (!$collector || $collector->role !== 'collector') {
            return redirect()->route('login');
        }

        return view('collector.edit-profile', compact('collector'));
    }

    /**
     * Actualiza los datos del perfil del recolector.
     */
    public function update(Request $request)
    {
        /** @var User $collector */
        $collector = Auth::user();

        if (!$collector || $collector->role !== 'collector') {
            return redirect()->route('login');
        }

        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,' . $collector->id,
            'company_name'   => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'profile_image'  => 'nullable|image|max:2048',
            'password'       => 'nullable|min:8|confirmed', // 🔒 confirmación requerida
        ]);

        // ✅ Actualizar campos básicos
        $collector->first_name   = $request->first_name;
        $collector->last_name    = $request->last_name;
        $collector->email        = $request->email;
        $collector->company_name = $request->company_name;
        $collector->address      = $request->address;
        $collector->latitude     = $request->latitude;
        $collector->longitude    = $request->longitude;

        // ✅ Imagen de perfil
        if ($request->hasFile('profile_image')) {
            // Eliminar la imagen anterior si existe
            if ($collector->profile_image_path && File::exists(public_path($collector->profile_image_path))) {
                File::delete(public_path($collector->profile_image_path));
            }

            $image = $request->file('profile_image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profiles'), $filename);

            $collector->profile_image_path = 'profiles/' . $filename;
        }

        // ✅ Contraseña (solo si se llena el campo)
        if ($request->filled('password')) {
            $collector->password = Hash::make($request->password);
        }

        $collector->save();

        return redirect()->route('collector.profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
