<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use Exception;

class RegisterDonorController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name'          => 'required|string|max:255',
                'last_name'           => 'required|string|max:255',
                'email'               => 'required|email|unique:users,email',
                'password'            => 'required|min:6|confirmed',
                'donor_type'          => 'required|in:family,organization',
                'organization_name'   => 'nullable|required_if:donor_type,organization|string|max:255',
                'representative_name' => 'nullable|required_if:donor_type,organization|string|max:255',
                'latitude'            => 'required|numeric',
                'longitude'           => 'required|numeric',
                'department'          => 'required|string|max:255',
                'province'            => 'required|string|max:255',
                'municipality'        => 'required|string|max:255',
                'address'             => 'required|string|max:255',
                'profile_image_path'  => 'nullable|image|max:2048'
            ]);

            // Forzar nulo si no es organización
            if ($request->donor_type !== 'organization') {
                $request->merge([
                    'organization_name' => null,
                    'representative_name' => null,
                ]);
            }

            $imagePath = null;
            if ($request->hasFile('profile_image_path')) {
                $filename = Str::uuid() . '.' . $request->file('profile_image_path')->getClientOriginalExtension();
                $request->file('profile_image_path')->move(public_path('profiles'), $filename);
                $imagePath = 'profiles/' . $filename;
            }

            User::create([
                'role'                => 'donor',
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'password'            => Hash::make($request->password),
                'donor_type'          => $request->donor_type,
                'organization_name'   => $request->organization_name,
                'representative_name' => $request->representative_name,
                'latitude'            => $request->latitude,
                'longitude'           => $request->longitude,
                'department'          => $request->department,
                'province'            => $request->province,
                'municipality'        => $request->municipality,
                'address'             => $request->address,
                'profile_image_path'  => $imagePath,
            ]);

            return redirect()->route('login')->with('success', 'Registro exitoso como donador.');
        } catch (Exception $e) {
            // 🔍 Mostrar error exacto en pantalla temporalmente
            dd([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }
    }
}
