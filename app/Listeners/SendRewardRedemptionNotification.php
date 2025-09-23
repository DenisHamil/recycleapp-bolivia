<?php

namespace App\Listeners;

use App\Events\RewardRedeemed;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SendRewardRedemptionNotification
{
    public function handle(RewardRedeemed $event)
    {
        $user = $event->user;
        $reward = $event->reward;
        $userReward = $event->userReward;

        Log::info('🎯 Iniciando listener RewardRedeemed', [
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'user_reward_id' => $userReward->id,
        ]);

        // Notificación para el donador
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Nuevo canje realizado',
            'message' => " ha canjeado una recompensa: {$reward->name}",
            'type' => 'reward_redemption.fulfilled',
            'related_id' => $userReward->id,
            'related_type' => 'reward_redemptions',
            'status' => 'new',
            'priority' => 3,
            'is_read' => false,
            'created_at' => now(),
        ]);

        Log::info('✅ Notificación creada para el donador', [
            'donor_id' => $user->id,
        ]);

        // 🔁 Notificación para el admin
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            Log::warning('⚠️ No se encontró ningún usuario con rol admin');
            return;
        }

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Nuevo canje realizado',
            'message' => "{$user->name} ha canjeado una recompensa: {$reward->name}",
            'type' => 'reward_redemption.fulfilled',
            'related_id' => $userReward->id,
            'related_type' => 'reward_redemptions',
            'status' => 'new',
            'priority' => 3,
            'is_read' => false,
            'created_at' => now(),
            'payload' => [
                'reward_name' => $reward->name,
                'success' => true,
            ]
        ]);

        Log::info('✅ Notificación creada para el admin', [
            'admin_id' => $admin->id,
        ]);
    }
}
