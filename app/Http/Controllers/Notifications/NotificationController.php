<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\RecyclerProposal;
use App\Models\User;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($request->routeIs('admin.*')) {
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('login');
            }

            // ✅ Obtener ID del administrador principal
            $adminId = User::where('role', 'admin')->value('id');

            $query = Notification::where('user_id', $adminId)
                ->where(function ($q) {
                    $q->where('related_type', 'reward_redemptions')
                      ->orWhere('type', 'like', 'reward_redemption.%');
                })
                ->orderByRaw('created_at IS NULL ASC')
                ->orderByDesc('created_at');
        } else {
            // 👤 Donador o Recolector
            $query = Notification::where('user_id', Auth::id())
                ->orderByRaw('created_at IS NULL ASC')
                ->orderByDesc('created_at');
        }

        $notifications = $query->paginate(15);

        $viewPath = $request->routeIs('admin.*')
            ? 'admin.notifications.inbox'
            : ($request->routeIs('collector.*') ? 'collector.notifications.inbox' : 'donor.notifications.inbox');

        return view($viewPath, compact('notifications'));
    }

    public function show(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $notification = Notification::findOrFail($id);

        if ($request->routeIs('admin.*')) {
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('login');
            }

            // 🔐 Asegurarse que el admin solo vea sus notificaciones
            $adminId = User::where('role', 'admin')->value('id');
            if ($notification->user_id !== $adminId) {
                abort(403, 'No tienes permiso para ver esta notificación.');
            }
        } else {
            if ($notification->user_id !== Auth::id()) {
                abort(403, 'No tienes permiso para ver esta notificación.');
            }
        }

        if (!$notification->is_read) {
            $notification->is_read = true;
            $notification->save();
        }

        $proposal = null;
        $relatedProposalMissing = false;

        if (
            in_array($notification->type, [
                'info',
                'proposal.received',
                'proposal.accepted',
                'rating.request',
            ]) && $notification->related_id
        ) {
            $proposal = RecyclerProposal::with([
                'collector',
                'donation.category',
                'donation.donor'
            ])
            ->where(function ($q) use ($notification) {
                $q->where('id', $notification->related_id)
                  ->orWhere('donation_id', $notification->related_id);
            })
            ->whereIn('status', ['accepted', 'completed', 'waiting', 'cancelled'])
            ->first();

            if (!$proposal) {
                $relatedProposalMissing = true;
            }
        }

        $viewPath = $request->routeIs('admin.*')
            ? 'admin.notifications.show'
            : ($request->routeIs('collector.*') ? 'collector.notifications.show' : 'donor.notifications.show');

        return view($viewPath, compact('notification', 'proposal', 'relatedProposalMissing'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$notification->is_read) {
            $notification->is_read = true;
            $notification->save();
        }

        return back()->with('success', 'Notificación marcada como leída.');
    }

    public function markAllRead()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'status' => 'required|in:new,processing,done,error',
        ]);

        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->status = $request->status;
        $notification->save();

        return back()->with('success', 'Estado de la notificación actualizado.');
    }
}
 