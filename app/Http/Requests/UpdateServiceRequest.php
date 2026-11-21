<?php

namespace App\Http\Requests;

use App\Enums\PricingType;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
        /** @var Service $service */
        $service = $this->route('service');

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:32',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('services', 'code')->ignore($service->id),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'pricing_type' => ['sometimes', 'required', 'string', Rule::enum(PricingType::class)],
            'base_price' => ['sometimes', 'required', 'integer', 'min:1', 'max:100000000'],
            'surcharge_percent' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'estimated_days' => ['sometimes', 'required', 'integer', 'min:1', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
