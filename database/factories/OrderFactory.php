<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_code' => 'CK-'.now('Asia/Jakarta')->format('Ymd').'-'.strtoupper(fake()->unique()->bothify('???###')),
            'service_id' => Service::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => '08'.fake()->numerify('##########'),
            'pickup_address' => fake()->address(),
            'status' => OrderStatus::WaitingPickup,
            'actual_weight_grams' => null,
            'billed_weight_kg' => null,
            'item_count' => null,
            'applied_unit_price' => null,
            'applied_surcharge_percent' => null,
            'final_amount' => null,
            'weighed_at' => null,
            'estimated_completion_at' => null,
            'delivered_at' => null,
            'payment_status' => PaymentStatus::Unpaid,
            'paid_at' => null,
        ];
    }
}
