<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Services\DailyRevenueReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Representasi publik pelacakan order tanpa PII (tidak mengekspos nama, telepon, atau alamat).
 *
 * @mixin Order
 */
class PublicOrderTrackingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tz = DailyRevenueReportService::REPORT_TIMEZONE;

        return [
            'order_code' => $this->order_code,
            'service' => [
                'code' => $this->service->code,
                'name' => $this->service->name,
                'pricing_type' => $this->service->pricing_type->value,
            ],
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'payment_status' => $this->payment_status->value,
            'payment_status_label' => $this->payment_status->label(),
            'billed_weight_kg' => $this->billed_weight_kg !== null ? (float) $this->billed_weight_kg : null,
            'item_count' => $this->item_count,
            'final_amount' => $this->final_amount,
            'weighed_at' => $this->weighed_at?->timezone($tz)->toIso8601String(),
            'estimated_completion_at' => $this->estimated_completion_at?->timezone($tz)->toIso8601String(),
            'delivered_at' => $this->delivered_at?->timezone($tz)->toIso8601String(),
            'created_at' => $this->created_at?->timezone($tz)->toIso8601String(),
        ];
    }
}
