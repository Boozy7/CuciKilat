<?php

namespace App\Enums;

enum PricingType: string
{
    case PerKg = 'per_kg';
    case PerItem = 'per_item';

    public function label(): string
    {
        return match ($this) {
            self::PerKg => 'Kiloan (per kg)',
            self::PerItem => 'Satuan (per item)',
        };
    }
}
