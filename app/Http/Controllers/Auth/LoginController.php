<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
  
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);


        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'collector' && $user->status === 'pending') {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'login' => 'Tu cuenta como recolector aún está pendiente de aprobación.',
                ]);
            }
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'donor':
                    return redirect()->route('donor.dashboard');
                case 'collector':
                    return redirect()->route('collector.dashboard');
                default:
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'login' => 'Rol de usuario no reconocido.',
                    ]);
            }
        }
        return back()->withErrors([
            'login' => 'Correo o contraseña incorrectos.',
        ])->onlyInput('email');
    }
}
