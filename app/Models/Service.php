<?php

namespace App\Models;

use App\Enums\PricingType;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'pricing_type',
        'base_price',
        'surcharge_percent',
        'estimated_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'pricing_type' => PricingType::class,
            'base_price' => 'integer',
            'surcharge_percent' => 'integer',
            'estimated_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isPerKg(): bool
    {
        return $this->pricing_type === PricingType::PerKg;
    }

    public function isPerItem(): bool
    {
        return $this->pricing_type === PricingType::PerItem;
    }
}
