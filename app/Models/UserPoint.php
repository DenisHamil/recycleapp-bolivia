<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserPoint extends Model
{
    use HasFactory;

    protected $table = 'user_points';
    public $incrementing = false;
    protected $keyType = 'string';

    // Tu tabla solo tiene created_at; por eso desactivamos timestamps automáticos
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'action',
        'points',
        'description',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
