<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'activity_log'; // tu tabla

    protected $fillable = [
        'id',
        'user_id',
        'action_type',
        'reference_table',
        'reference_id',
        'detail',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            if (!$m->id) {
                $m->id = (string) Str::uuid();
            }
            if (!$m->created_at) {
                $m->created_at = now();
            }
        });
    }

    /**
     * Registra una entrada de historial.
     */
    public static function record(
        ?string $userId,
        ?string $actorId, // no se guarda; solo compat
        string $type,
        ?string $refTable = null,
        ?string $refId = null,
        ?string $detail = null
    ): self {
        return static::create([
            'user_id'        => $userId,
            'action_type'    => $type,
            'reference_table'=> $refTable,
            'reference_id'   => $refId,
            'detail'         => $detail,
            'created_at'     => now(),
        ]);
    }

    /**
     * Accesor para mostrar el campo "detail" formateado.
     */
    public function getDetailFormattedAttribute(): string
    {
        $decoded = json_decode($this->detail, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Caso especial para calificación
            if (isset($decoded['stars'])) {
                return "⭐ {$decoded['stars']} estrellas (por {$decoded['by']})";
            }

            // Otro JSON cualquiera -> key: value
            return collect($decoded)
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->implode(', ');
        }

        // Si no es JSON, devolver como texto normal
        return $this->detail ?? '';
    }
}
