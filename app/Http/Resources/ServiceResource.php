<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'pricing_type' => $this->pricing_type->value,
            'pricing_type_label' => $this->pricing_type->label(),
            'base_price' => $this->base_price,
            'surcharge_percent' => $this->surcharge_percent,
            'estimated_days' => $this->estimated_days,
            'is_active' => $this->is_active,
        ];
    }
}
