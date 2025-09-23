<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\RewardStore;
use App\Models\UserReward;

class RewardRedeemedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reward;
    public $userReward;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, RewardStore $reward, UserReward $userReward)
    {
        $this->user = $user;
        $this->reward = $reward;
        $this->userReward = $userReward;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Confirmación de canje de recompensa')
            ->view('emails.reward_redeemed_user');
    }
}
