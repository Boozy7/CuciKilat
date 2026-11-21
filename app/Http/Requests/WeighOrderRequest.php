<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class WeighOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var ?Order $order */
        $order = $this->route('order');
        $service = $order?->service;

        if ($service !== null && $service->isPerItem()) {
            return [
                'item_count' => ['required', 'integer', 'min:1', 'max:1000'],
                'actual_weight_grams' => ['nullable', 'prohibited'],
            ];
        }

        if ($service !== null && $service->isPerKg()) {
            return [
                'actual_weight_grams' => ['required', 'integer', 'min:1', 'max:500000'],
                'item_count' => ['nullable', 'prohibited'],
            ];
        }

        return [
            'actual_weight_grams' => ['nullable', 'integer', 'min:1', 'max:500000'],
            'item_count' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
