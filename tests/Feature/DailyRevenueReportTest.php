<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyRevenueReportTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create([
            'email' => 'petugas@cucikilat.local',
            'password' => 'secret123',
        ]);
    }

    public function test_staff_can_login_and_logout(): void
    {
        $this->postJson('/login', [
            'email' => 'petugas@cucikilat.local',
            'password' => 'wrong-password',
        ])->assertUnprocessable();

        $this->postJson('/login', [
            'email' => 'petugas@cucikilat.local',
            'password' => 'secret123',
        ])
            ->assertOk()
            ->assertJsonPath('user.email', 'petugas@cucikilat.local');

        $this->getJson('/staff/orders')->assertOk();

        $this->postJson('/logout')->assertOk();
        $this->assertGuest();
    }

    public function test_payment_cannot_be_recorded_before_order_is_billed(): void
    {
        $service = Service::factory()->create();
        $order = Order::factory()->create([
            'service_id' => $service->id,
            'status' => OrderStatus::PickedUp,
            'final_amount' => null,
            'weighed_at' => null,
        ]);

        $this->actingAs($this->staff)
            ->patchJson("/staff/orders/{$order->id}/payment", ['payment_status' => 'paid'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_status']);
    }

    public function test_daily_revenue_report_sums_only_paid_orders_using_wib_date_boundaries(): void
    {
        $service = Service::factory()->create();

        // 1. Paid pada 2026-11-21 01:30 WIB (= 2026-11-20 18:30 UTC) -> HARUS masuk laporan 2026-11-21 WIB
        $earlyMorningWibOrder = Order::factory()->create([
            'service_id' => $service->id,
            'status' => OrderStatus::Processing,
            'final_amount' => 31500,
            'weighed_at' => CarbonImmutable::parse('2026-11-20 18:00:00', 'UTC'),
            'payment_status' => PaymentStatus::Paid,
            'paid_at' => CarbonImmutable::parse('2026-11-20 18:30:00', 'UTC'),
        ]);

        // 2. Paid pada 2026-11-21 23:45 WIB (= 2026-11-21 16:45 UTC) -> HARUS masuk laporan 2026-11-21 WIB
        $lateNightWibOrder = Order::factory()->create([
            'service_id' => $service->id,
            'status' => OrderStatus::Delivered,
            'final_amount' => 17500,
            'weighed_at' => CarbonImmutable::parse('2026-11-21 10:00:00', 'UTC'),
            'delivered_at' => CarbonImmutable::parse('2026-11-21 16:50:00', 'UTC'),
            'payment_status' => PaymentStatus::Paid,
            'paid_at' => CarbonImmutable::parse('2026-11-21 16:45:00', 'UTC'),
        ]);

        // 3. Pending pada 2026-11-21 WIB -> HARUS DIKECUALIKAN
        Order::factory()->create([
            'service_id' => $service->id,
            'status' => OrderStatus::Processing,
            'final_amount' => 21000,
            'weighed_at' => CarbonImmutable::parse('2026-11-21 05:00:00', 'UTC'),
            'payment_status' => PaymentStatus::Pending,
            'paid_at' => null,
        ]);

        // 4. Unpaid pada 2026-11-21 WIB -> HARUS DIKECUALIKAN
        Order::factory()->create([
            'service_id' => $service->id,
            'status' => OrderStatus::ReadyForDelivery,
            'final_amount' => 14000,
            'weighed_at' => CarbonImmutable::parse('2026-11-21 06:00:00', 'UTC'),
            'payment_status' => PaymentStatus::Unpaid,
            'paid_at' => null,
        ]);

        // 5. Paid pada 2026-11-20 23:30 WIB (= 2026-11-20 16:30 UTC) -> masuk 2026-11-20 WIB, bukan 2026-11-21 WIB
        Order::factory()->create([
            'service_id' => $service->id,
            'status' => OrderStatus::Delivered,
            'final_amount' => 15000,
            'weighed_at' => CarbonImmutable::parse('2026-11-20 15:00:00', 'UTC'),
            'payment_status' => PaymentStatus::Paid,
            'paid_at' => CarbonImmutable::parse('2026-11-20 16:30:00', 'UTC'),
        ]);

        $response21 = $this->actingAs($this->staff)
            ->getJson('/staff/reports/daily-revenue?date=2026-11-21');

        $response21
            ->assertOk()
            ->assertJsonPath('date', '2026-11-21')
            ->assertJsonPath('timezone', 'Asia/Jakarta')
            ->assertJsonPath('paid_orders_count', 2)
            ->assertJsonPath('total_revenue', 49000);

        $returnedIds = collect($response21->json('orders'))->pluck('id')->all();
        $this->assertSame([$earlyMorningWibOrder->id, $lateNightWibOrder->id], $returnedIds);

        // Cek laporan hari sebelumnya (2026-11-20 WIB) -> hanya 15000, tidak tercampur order 01:30 WIB tanggal 21
        $this->actingAs($this->staff)
            ->getJson('/staff/reports/daily-revenue?date=2026-11-20')
            ->assertOk()
            ->assertJsonPath('paid_orders_count', 1)
            ->assertJsonPath('total_revenue', 15000);
    }
}
