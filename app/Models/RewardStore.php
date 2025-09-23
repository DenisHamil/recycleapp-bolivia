<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Concerns\HasUuids;
    use Illuminate\Database\Eloquent\SoftDeletes; 
    use Illuminate\Support\Str;

    class RewardStore extends Model
    {
        use HasFactory, HasUuids, SoftDeletes; 

        protected $table = 'rewards_store';
        public $incrementing = false;
        protected $keyType = 'string';

        protected $fillable = [
            'id',
            'name',
            'description',
            'image_path',
            'points_required',
            'stock',
            'reward_category_id',
            'commerce_id',
            'is_monthly_promo',
        ];

        protected $casts = [
            'is_monthly_promo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        protected static function boot()
        {
            parent::boot();
            static::creating(function ($m) {
                if (empty($m->id)) {
                    $m->id = (string) Str::uuid();
                }
            });
        }

        /** Relaciones */
        public function images(): HasMany
        {
            return $this->hasMany(Image::class, 'reward_id');
        }

        public function category(): BelongsTo
        {
            return $this->belongsTo(RewardCategory::class, 'reward_category_id');
        }

        public function commerce(): BelongsTo
        {
            return $this->belongsTo(Commerce::class, 'commerce_id');
        }

        public function redemptions(): HasMany
        {
            return $this->hasMany(UserReward::class, 'reward_id');
        }
    }
