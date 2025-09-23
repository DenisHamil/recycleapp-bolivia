<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Category;
use App\Models\Donation;
use App\Models\Image;

class DonationController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('donor.donations', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'          => 'required|exists:categories,id',
            'description'          => 'nullable|string|max:1000',
            'estimated_weight'     => 'nullable|numeric|min:0.1',
            'available_from_date'  => 'required|date|after_or_equal:today',
            'available_until_date' => 'required|date|after_or_equal:available_from_date',
            'available_from_time'  => 'required',
            'available_until_time' => 'required',
            'latitude'             => 'required|numeric',
            'longitude'            => 'required|numeric',
            'address_description'  => 'nullable|string|max:255',
            'image'                => 'required|image|max:4096', // 4MB
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($request, $userId) {
            // 1) Crear la donación
            $donation = Donation::create([
                'id'                   => (string) Str::uuid(),
                'donor_id'             => $userId,
                'category_id'          => $request->category_id,
                'description'          => $request->description,
                'estimated_weight'     => $request->estimated_weight,
                'available_from_date'  => $request->available_from_date,
                'available_until_date' => $request->available_until_date,
                'available_from_time'  => $request->available_from_time,
                'available_until_time' => $request->available_until_time,
                'latitude'             => $request->latitude,
                'longitude'            => $request->longitude,
                'address_description'  => $request->address_description,
                'state'                => 'pending', // opcional, pero útil para el flujo
            ]);

            // 2) Guardar imagen (si viene)
            if ($request->hasFile('image')) {
                $image    = $request->file('image');
                $ext      = $image->getClientOriginalExtension();
                $filename = (string) Str::uuid().'.'.$ext;

                // Mantengo tu esquema de guardado en /public/residuos
                $image->move(public_path('residuos'), $filename);

                Image::create([
                    'id'          => (string) Str::uuid(),
                    'donation_id' => $donation->id,
                    'path'        => 'residuos/'.$filename,
                    'type'        => 'residue',
                ]);
            }

            // 3) Registrar en activity_log (para el historial del donador)
            DB::table('activity_log')->insert([
                'id'              => (string) Str::uuid(),
                'user_id'         => $userId,
                'action_type'     => 'donation.created',
                'reference_table' => 'donations',
                'reference_id'    => $donation->id,
                'detail'          => 'Donación creada',
                'created_at'      => now(),
            ]);
        });

        return redirect()
            ->route('donor.dashboard')
            ->with('success', 'Donación publicada correctamente.');
    }
}
