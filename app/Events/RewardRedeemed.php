<?php

namespace App\Events;

use App\Models\User;
use App\Models\RewardStore;
use App\Models\UserReward;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RewardRedeemed
{
    use Dispatchable, SerializesModels;

    public User $user;
    public RewardStore $reward;
    public UserReward $userReward;

    public function __construct(User $user, RewardStore $reward, UserReward $userReward)
    {
        $this->user = $user;
        $this->reward = $reward;
        $this->userReward = $userReward;
    }
}
