<?php

namespace Tests\Unit;

use App\Enums\PricingType;
use App\Models\Service;
use App\Services\OrderPricingCalculator;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class OrderPricingCalculatorTest extends TestCase
{
    private OrderPricingCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new OrderPricingCalculator;
    }

    public function test_kiloan_enforces_minimum_two_kg_and_rounds_up_by_half_kg(): void
    {
        $this->assertSame('2.00', $this->calculator->calculateBilledWeightKg(1200));
        $this->assertSame('2.00', $this->calculator->calculateBilledWeightKg(2000));
        $this->assertSame('2.50', $this->calculator->calculateBilledWeightKg(2001));
        $this->assertSame('2.50', $this->calculator->calculateBilledWeightKg(2500));
        $this->assertSame('3.00', $this->calculator->calculateBilledWeightKg(2510));
    }

    public function test_reguler_calculates_base_price_and_three_day_estimate(): void
    {
        $service = new Service([
            'code' => 'REGULER',
            'name' => 'Reguler',
            'pricing_type' => PricingType::PerKg,
            'base_price' => 7000,
            'surcharge_percent' => 0,
            'estimated_days' => 3,
        ]);

        $weighedAt = CarbonImmutable::parse('2026-11-21 10:00:00', 'Asia/Jakarta');

        $underMin = $this->calculator->calculate($service, 1200, null, $weighedAt);
        $this->assertSame('2.00', $underMin['billed_weight_kg']);
        $this->assertSame(14000, $underMin['final_amount']);
        $this->assertSame(7000, $underMin['applied_unit_price']);
        $this->assertSame(0, $underMin['applied_surcharge_percent']);
        $this->assertTrue($underMin['estimated_completion_at']->equalTo($weighedAt->addDays(3)));

        $exactHalf = $this->calculator->calculate($service, 2500, null, $weighedAt);
        $this->assertSame('2.50', $exactHalf['billed_weight_kg']);
        $this->assertSame(17500, $exactHalf['final_amount']);

        $overHalf = $this->calculator->calculate($service, 2510, null, $weighedAt);
        $this->assertSame('3.00', $overHalf['billed_weight_kg']);
        $this->assertSame(21000, $overHalf['final_amount']);
    }

    public function test_express_applies_fifty_percent_surcharge_and_one_day_estimate(): void
    {
        $service = new Service([
            'code' => 'EXPRESS',
            'name' => 'Express',
            'pricing_type' => PricingType::PerKg,
            'base_price' => 7000,
            'surcharge_percent' => 50,
            'estimated_days' => 1,
        ]);

        $weighedAt = CarbonImmutable::parse('2026-11-21 10:00:00', 'Asia/Jakarta');
        $result = $this->calculator->calculate($service, 2510, null, $weighedAt);

        $this->assertSame(2510, $result['actual_weight_grams']);
        $this->assertSame('3.00', $result['billed_weight_kg']);
        $this->assertSame(7000, $result['applied_unit_price']);
        $this->assertSame(50, $result['applied_surcharge_percent']);
        $this->assertSame(31500, $result['final_amount']);
        $this->assertTrue($result['estimated_completion_at']->equalTo($weighedAt->addDay()));
    }

    public function test_satuan_calculates_per_item_price_and_master_service_estimate(): void
    {
        $service = new Service([
            'code' => 'SATUAN',
            'name' => 'Satuan',
            'pricing_type' => PricingType::PerItem,
            'base_price' => 15000,
            'surcharge_percent' => 0,
            'estimated_days' => 2,
        ]);

        $weighedAt = CarbonImmutable::parse('2026-11-21 10:00:00', 'Asia/Jakarta');
        $result = $this->calculator->calculate($service, null, 3, $weighedAt);

        $this->assertNull($result['actual_weight_grams']);
        $this->assertNull($result['billed_weight_kg']);
        $this->assertSame(3, $result['item_count']);
        $this->assertSame(45000, $result['final_amount']);
        $this->assertTrue($result['estimated_completion_at']->equalTo($weighedAt->addDays(2)));
    }
}
