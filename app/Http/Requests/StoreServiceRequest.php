<?php

namespace App\Http\Requests;

use App\Enums\PricingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge([
                'code' => strtoupper(trim($this->input('code'))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:32', 'regex:/^[A-Z0-9_-]+$/', 'unique:services,code'],
            'name' => ['required', 'string', 'max:100'],
            'pricing_type' => ['required', 'string', Rule::enum(PricingType::class)],
            'base_price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'surcharge_percent' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
