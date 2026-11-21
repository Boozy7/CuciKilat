<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Services\DailyRevenueReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class StaffOrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tz = DailyRevenueReportService::REPORT_TIMEZONE;

        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'pickup_address' => $this->pickup_address,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'actual_weight_grams' => $this->actual_weight_grams,
            'billed_weight_kg' => $this->billed_weight_kg !== null ? (float) $this->billed_weight_kg : null,
            'item_count' => $this->item_count,
            'applied_unit_price' => $this->applied_unit_price,
            'applied_surcharge_percent' => $this->applied_surcharge_percent,
            'final_amount' => $this->final_amount,
            'weighed_at' => $this->weighed_at?->timezone($tz)->toIso8601String(),
            'estimated_completion_at' => $this->estimated_completion_at?->timezone($tz)->toIso8601String(),
            'delivered_at' => $this->delivered_at?->timezone($tz)->toIso8601String(),
            'payment_status' => $this->payment_status->value,
            'payment_status_label' => $this->payment_status->label(),
            'paid_at' => $this->paid_at?->timezone($tz)->toIso8601String(),
            'created_at' => $this->created_at?->timezone($tz)->toIso8601String(),
            'updated_at' => $this->updated_at?->timezone($tz)->toIso8601String(),
        ];
    }
}
