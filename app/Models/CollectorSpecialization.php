<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectorSpecialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'collector_id',   // ID del recolector (user_id)
        'category_id',    // ID de la categoría
    ];

    // Asegura que la clave primaria sea un string (UUID)
    protected $keyType = 'string';  
    public $incrementing = false;  

    // Relación con el modelo User (un recolector tiene muchas especializaciones)
    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id', 'id');  // Relación inversa
    }

    // Relación con el modelo Category (una especialización pertenece a una categoría)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');  // Relación inversa
    }
}
