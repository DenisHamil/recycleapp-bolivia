<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\User;

class DonorProfileController extends Controller
{
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('donor.profile', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'nullable|string|max:100',
            'email'               => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'organization_name'   => 'nullable|string|max:255',
            'representative_name' => 'nullable|string|max:255',
            'latitude'            => 'nullable|numeric',
            'longitude'           => 'nullable|numeric',
            'profile_image'       => 'nullable|image|max:2048', // Máx 2MB
            'password'            => 'nullable|min:8|confirmed', // 🔒 nueva validación
        ]);

        // ✅ Procesar imagen si se sube una nueva
        if ($request->hasFile('profile_image')) {
            // Eliminar anterior si existe
            if ($user->profile_image_path && File::exists(public_path($user->profile_image_path))) {
                File::delete(public_path($user->profile_image_path));
            }

            $image = $request->file('profile_image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profiles'), $filename);

            $user->profile_image_path = 'profiles/' . $filename;
        }

        // ✅ Actualizar datos básicos
        $user->first_name         = $request->first_name;
        $user->last_name          = $request->last_name;
        $user->email              = $request->email;
        $user->organization_name  = $request->organization_name;
        $user->representative_name = $request->representative_name;
        $user->latitude           = $request->latitude;
        $user->longitude          = $request->longitude;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('donor.profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
