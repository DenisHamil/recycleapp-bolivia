<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;
use App\Models\User;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';
    public $incrementing = false; // Usamos UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'donor_id',
        'collector_id',
        'category_id',
        'subcategory_id',
        'description',
        'estimated_weight',
        'confirmed_weight',
        'latitude',
        'longitude',
        'address_description',
        'available_from_date',
        'available_until_date',
        'available_from_time',
        'available_until_time',
        'state',
        'cancel_reason',
        'finalized_at',
        'confirmed_by_collector',
    ];

    /**
     * Relación con la categoría del residuo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con el donador (usuario que publicó la donación)
     */
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    /**
     * Relación con el recolector (opcional)
     */
    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    /**
     * Imágenes asociadas a esta donación
     */
    public function images()
    {
        return $this->hasMany(\App\Models\Image::class);
    }
}
