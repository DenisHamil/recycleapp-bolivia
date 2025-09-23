<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'category_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subcategory) {
            if (!$subcategory->id) {
                $subcategory->id = (string) Str::uuid();
            }
        });
    }

    // 🔁 Relación inversa con categoría
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // 🔁 Relación con donaciones
    public function donations()
    {
        return $this->hasMany(Donation::class, 'subcategory_id', 'id');
    }
}
