<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\RecyclerProposal;

class Notification extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'related_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notification) {
            if (!$notification->id) {
                $notification->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 Relación con propuesta
    public function proposal()
    {
        return $this->belongsTo(RecyclerProposal::class, 'related_id');
    }

    // Scope para no leídas
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
