<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Rating extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'ratings';

    protected $fillable = [
        'id',
        'from_user_id',
        'to_user_id',
        'donation_id',
        'stars',
        'comment',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'stars' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            if (!$m->id) $m->id = (string) Str::uuid();
            if (!$m->created_at) $m->created_at = now();
        });
    }
}
