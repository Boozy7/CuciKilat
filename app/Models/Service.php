<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'code',
        'name',
        'pricing_type',
        'base_price',
        'surcharge_percent',
        'estimated_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'surcharge_percent' => 'integer',
            'estimated_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
