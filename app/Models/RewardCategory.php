<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RewardCategory extends Model
{
    // Tu migración solo define created_at, sin updated_at -> mejor desactivar timestamps
    public $timestamps = false;

    protected $table = 'reward_categories';

    protected $fillable = [
        'name',
        'icon_path',
        'created_at',
    ];

    public function rewards(): HasMany
    {
        return $this->hasMany(RewardStore::class, 'reward_category_id');
    }
}
