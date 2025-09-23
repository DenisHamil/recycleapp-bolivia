<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Donation;

class RecyclerProposal extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'donation_id',
        'collector_id',
        'proposed_date',
        'proposed_time',
        'status',
        'created_at',
    ];

    protected $casts = [
        'proposed_date' => 'date',
        'proposed_time' => 'datetime:H:i:s',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($proposal) {
            if (!$proposal->id) {
                $proposal->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Recolector que hizo la propuesta
     */
    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    /**
     * Donación relacionada a la propuesta
     */
    public function donation()
    {
        return $this->belongsTo(Donation::class, 'donation_id');
    }
}
