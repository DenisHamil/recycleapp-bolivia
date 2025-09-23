<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;

class RegisterCollectorController extends Controller
{
    // Muestra el formulario
    public function showForm()
    {
        $categories = Category::all();  
        return view('auth.register.collector', compact('categories'));
    }

    // Procesa el formulario
    public function store(Request $request)
    {
        // Validación del formulario
        $request->validate([
            'company_name'        => 'required|string|max:255',
            'representative_name' => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'password'            => 'required|min:6|confirmed',
            'latitude'            => 'required|numeric',
            'longitude'           => 'required|numeric',
            'department'          => 'required|string|max:255',
            'province'            => 'required|string|max:255',
            'municipality'        => 'required|string|max:255',
            'address'             => 'required|string|max:255',
            'category_id'         => 'required|array',
            'category_id.*'       => 'exists:categories,id',
            'profile_image_path'  => 'nullable|image|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $imagePath = null;

            if ($request->hasFile('profile_image_path')) {
                $filename = Str::uuid() . '.' . $request->file('profile_image_path')->getClientOriginalExtension();
                $request->file('profile_image_path')->move(public_path('profiles'), $filename);
                $imagePath = 'profiles/' . $filename;
            }

            $user = User::create([
                'role'                => 'collector',
                'company_name'        => $request->company_name,
                'representative_name' => $request->representative_name,
                'email'               => $request->email,
                'password'            => Hash::make($request->password),
                'latitude'            => $request->latitude,
                'longitude'           => $request->longitude,
                'department'          => $request->department,
                'province'            => $request->province,
                'municipality'        => $request->municipality,
                'address'             => $request->address,
                'profile_image_path'  => $imagePath,
                'status'              => 'pending',
            ]);

            foreach ($request->category_id as $categoryId) {
                DB::table('collector_specializations')->insert([
                    'id'           => Str::uuid(),
                    'collector_id' => $user->id,
                    'category_id'  => $categoryId
                ]);
            }

            DB::commit();

            return redirect()->route('login')->with('success', 'Registro exitoso como recolector. Esperando aprobación.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }
}
