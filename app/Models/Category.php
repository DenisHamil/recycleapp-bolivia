<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'color',             // ✅ Nuevo campo
        'points_per_kilo',   // ✅ Nuevo campo
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (!$category->id) {
                $category->id = (string) Str::uuid(); // Generar UUID si no está definido
            }
        });
    }

    // Relación con subcategorías
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id', 'id');
    }

    // Relación con donaciones
    public function donations()
    {
        return $this->hasMany(Donation::class, 'category_id', 'id');
    }
}
