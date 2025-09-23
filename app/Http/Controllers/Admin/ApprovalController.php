<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\CollectorAcceptedMail;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    // Método para listar todos los recolectores pendientes de aprobación
    public function index()
    {
        $collectors = User::where('role', 'collector')
                          ->where('status', 'pending')
                          ->get();

        return view('admin.modules.approvals', compact('collectors'));
    }

    // Método para aprobar un recolector
    public function accept($id)
    {
        $collector = User::findOrFail($id);

        if ($collector->status !== 'pending') {
            return back()->with('error', 'Este recolector ya ha sido aprobado o rechazado.');
        }

        $collector->status = 'active';
        $collector->save();

        Mail::to($collector->email)->send(new CollectorAcceptedMail($collector));

        return redirect()->route('admin.approvals')->with('success', 'Recolector aprobado y notificado.');
    }

    // Método para rechazar un recolector
    public function reject($id)
    {
        $collector = User::findOrFail($id);

        if ($collector->status !== 'pending') {
            return back()->with('error', 'Este recolector ya ha sido aprobado o rechazado.');
        }

        $collector->status = 'inactive';
        $collector->save();

        return redirect()->route('admin.approvals')->with('success', 'Recolector rechazado.');
    }
}
