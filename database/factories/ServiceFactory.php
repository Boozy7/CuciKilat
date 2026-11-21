<?php

namespace Database\Factories;

use App\Enums\PricingType;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('SVC????')),
            'name' => fake()->words(2, true),
            'pricing_type' => PricingType::PerKg,
            'base_price' => 7000,
            'surcharge_percent' => 0,
            'estimated_days' => 3,
            'is_active' => true,
        ];
    }

    public function express(): static
    {
        return $this->state(fn (): array => [
            'code' => 'EXPRESS',
            'name' => 'Express',
            'pricing_type' => PricingType::PerKg,
            'base_price' => 7000,
            'surcharge_percent' => 50,
            'estimated_days' => 1,
            'is_active' => true,
        ]);
    }

    public function perItem(): static
    {
        return $this->state(fn (): array => [
            'pricing_type' => PricingType::PerItem,
            'base_price' => 15000,
            'surcharge_percent' => 0,
            'estimated_days' => 2,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
