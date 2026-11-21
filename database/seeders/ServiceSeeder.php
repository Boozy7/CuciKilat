<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Tarif di bawah adalah data demo dan dapat diubah dari master layanan.
        $demoServices = [
            [
                'code' => 'REGULER',
                'name' => 'Reguler',
                'pricing_type' => 'per_kg',
                'base_price' => 7000,
                'surcharge_percent' => 0,
                'estimated_days' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'EXPRESS',
                'name' => 'Express',
                'pricing_type' => 'per_kg',
                'base_price' => 7000,
                'surcharge_percent' => 50,
                'estimated_days' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'SATUAN',
                'name' => 'Satuan',
                'pricing_type' => 'per_item',
                'base_price' => 15000,
                'surcharge_percent' => 0,
                'estimated_days' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($demoServices as $service) {
            Service::updateOrCreate(
                ['code' => $service['code']],
                $service,
            );
        }
    }
}
