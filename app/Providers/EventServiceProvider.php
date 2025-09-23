<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Events\RewardRedeemed;
use App\Listeners\SendRewardRedemptionNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // ✅ Registra el listener
        RewardRedeemed::class => [
            SendRewardRedemptionNotification::class,
        ],
    ];
}
