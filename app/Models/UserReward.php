<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class UserReward extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_rewards';
    public $incrementing = false;
    protected $keyType = 'string';

    // Tu tabla actual NO tiene updated_at, solo redeemed_at; por eso desactivamos timestamps
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'reward_id',
        'status',
        'redeemed_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->id)) {
                $m->id = (string) Str::uuid();
            }
        });
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(RewardStore::class, 'reward_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
