<?php

namespace App\Models;

use App\Models\UserLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;



/**
 * @property string $id
 * @property string $role
 * @property \Illuminate\Database\Eloquent\Collection $collectorSpecializations
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'donor_type',
        'organization_name',
        'representative_name',
        'company_name',
        'role',
        'latitude',
        'longitude',
        'department',
        'province',
        'municipality',
        'address',
        'profile_image_path',
        'level',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->id) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    public function collectorSpecializations(): HasMany
    {
        return $this->hasMany(CollectorSpecialization::class, 'collector_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'donor_id');
    }

    public function collectedDonations(): HasMany
    {
        return $this->hasMany(Donation::class, 'collector_id');
    }

    public function pointsHistory(): HasMany
    {
        return $this->hasMany(UserPoint::class);
    }

    // -----------------------
    // NUEVOS MÉTODOS DE NIVEL
    // -----------------------

    public function getTotalPointsAttribute(): int
    {
        return $this->pointsHistory()->sum('points');
    }

    public function getCurrentLevelAttribute(): ?UserLevel
    {
        return UserLevel::where('min_points', '<=', $this->total_points)
            ->orderByDesc('min_points')
            ->first();
    }

    public function getNextLevelAttribute(): ?UserLevel
    {
        return UserLevel::where('min_points', '>', $this->total_points)
            ->orderBy('min_points')
            ->first();
    }

    public function getPointsToNextLevelAttribute(): ?int
    {
        $nextLevel = $this->next_level;
        return $nextLevel ? $nextLevel->min_points - $this->total_points : null;
    }

    public function getProgressPercentageAttribute(): int
    {
        $current = $this->current_level;
        $next = $this->next_level;

        if (!$current || !$next) {
            return 100; // Ya está en el nivel más alto
        }

        $range = $next->min_points - $current->min_points;
        if ($range === 0) return 100;

        return (int) (100 * ($this->total_points - $current->min_points) / $range);
    }
}
