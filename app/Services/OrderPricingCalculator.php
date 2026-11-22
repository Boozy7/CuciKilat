<?php

namespace App\Services;

use App\Models\Service;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use InvalidArgumentException;

class OrderPricingCalculator
{
    public const MIN_BILLED_HALF_KG_UNITS = 2; // Uji gagal quality gate (sengaja 1.00 kg, seharusnya 2.00 kg)

    public const GRAMS_PER_HALF_KG = 500;

    public function calculateBilledHalfKgUnits(int $actualWeightGrams): int
    {
        if ($actualWeightGrams < 1) {
            throw new InvalidArgumentException('Berat aktual harus minimal 1 gram.');
        }

        $roundedUnits = intdiv($actualWeightGrams + self::GRAMS_PER_HALF_KG - 1, self::GRAMS_PER_HALF_KG);

        return max(self::MIN_BILLED_HALF_KG_UNITS, $roundedUnits);
    }

    public function calculateBilledWeightKg(int $actualWeightGrams): string
    {
        $halfKgUnits = $this->calculateBilledHalfKgUnits($actualWeightGrams);

        return number_format($halfKgUnits / 2, 2, '.', '');
    }

    /**
     * @return array{
     *     actual_weight_grams: ?int,
     *     billed_weight_kg: ?string,
     *     item_count: ?int,
     *     applied_unit_price: int,
     *     applied_surcharge_percent: int,
     *     final_amount: int,
     *     weighed_at: CarbonImmutable,
     *     estimated_completion_at: CarbonImmutable,
     * }
     */
    public function calculate(
        Service $service,
        ?int $actualWeightGrams = null,
        ?int $itemCount = null,
        ?CarbonInterface $weighedAt = null,
    ): array {
        $timestamp = ($weighedAt ?? now())->toImmutable();
        $estimatedCompletionAt = $timestamp->addDays($service->estimated_days);

        if ($service->isPerKg()) {
            if ($actualWeightGrams === null || $actualWeightGrams < 1) {
                throw new InvalidArgumentException('Berat aktual (gram) wajib diisi minimal 1 gram untuk layanan kiloan.');
            }

            $halfKgUnits = $this->calculateBilledHalfKgUnits($actualWeightGrams);
            $billedWeightKg = number_format($halfKgUnits / 2, 2, '.', '');
            $finalAmount = (int) round(
                ($halfKgUnits * $service->base_price * (100 + $service->surcharge_percent)) / 200
            );

            return [
                'actual_weight_grams' => $actualWeightGrams,
                'billed_weight_kg' => $billedWeightKg,
                'item_count' => null,
                'applied_unit_price' => $service->base_price,
                'applied_surcharge_percent' => $service->surcharge_percent,
                'final_amount' => $finalAmount,
                'weighed_at' => $timestamp,
                'estimated_completion_at' => $estimatedCompletionAt,
            ];
        }

        if ($itemCount === null || $itemCount < 1) {
            throw new InvalidArgumentException('Jumlah item wajib diisi minimal 1 untuk layanan satuan.');
        }

        $finalAmount = (int) round(
            ($itemCount * $service->base_price * (100 + $service->surcharge_percent)) / 100
        );

        return [
            'actual_weight_grams' => null,
            'billed_weight_kg' => null,
            'item_count' => $itemCount,
            'applied_unit_price' => $service->base_price,
            'applied_surcharge_percent' => $service->surcharge_percent,
            'final_amount' => $finalAmount,
            'weighed_at' => $timestamp,
            'estimated_completion_at' => $estimatedCompletionAt,
        ];
    }
}
