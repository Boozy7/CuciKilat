<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_code',
        'service_id',
        'customer_name',
        'customer_phone',
        'pickup_address',
        'status',
        'actual_weight_grams',
        'billed_weight_kg',
        'item_count',
        'applied_unit_price',
        'applied_surcharge_percent',
        'final_amount',
        'weighed_at',
        'estimated_completion_at',
        'delivered_at',
        'payment_status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'actual_weight_grams' => 'integer',
            'billed_weight_kg' => 'decimal:2',
            'item_count' => 'integer',
            'applied_unit_price' => 'integer',
            'applied_surcharge_percent' => 'integer',
            'final_amount' => 'integer',
            'weighed_at' => 'datetime',
            'estimated_completion_at' => 'datetime',
            'delivered_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Order $order): void {
            if ($order->getRawOriginal('status') === OrderStatus::Delivered->value) {
                throw ValidationException::withMessages([
                    'order' => ['Order yang sudah diserahkan tidak dapat diubah.'],
                ]);
            }
        });
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function isDelivered(): bool
    {
        return $this->status === OrderStatus::Delivered;
    }

    public function isWeighed(): bool
    {
        return $this->final_amount !== null && $this->weighed_at !== null;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid;
    }
}
