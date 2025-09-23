<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Donation;
use App\Models\RewardStore; // 👈 IMPORTANTE

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'donation_id',
        'reward_id',
        'path',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function reward()
    {
        return $this->belongsTo(RewardStore::class);
    }
}
