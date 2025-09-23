<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Commerce extends Model
{
    use HasUuids;

    protected $table = 'commerce';
    public $incrementing = false;
    protected $keyType = 'string';

    // Tu migración solo define created_at, sin updated_at
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'logo_path',
        'description',
        'created_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->id)) {
                $m->id = (string) Str::uuid();
            }
            if (empty($m->created_at)) {
                $m->created_at = now();
            }
        });
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(RewardStore::class, 'commerce_id');
    }
}
