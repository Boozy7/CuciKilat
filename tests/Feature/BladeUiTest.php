<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BladeUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ServiceSeeder::class);
    }

    public function test_customer_can_open_home_create_order_and_track_without_pii(): void
    {
        $express = Service::where('code', 'EXPRESS')->firstOrFail();

        $this->get('/')
            ->assertOk()
            ->assertSee('CuciKilat')
            ->assertSee('Express');

        $this->get('/orders/create')
            ->assertOk()
            ->assertSee('Pesan Jemput Laundry');

        $createResponse = $this->post('/orders', [
            'service_id' => $express->id,
            'customer_name' => 'Rahasia Uji Blade',
            'customer_phone' => '081234000999',
            'pickup_address' => 'Jl. Privasi Blok Z No. 7',
        ]);

        $order = Order::firstOrFail();

        $createResponse->assertRedirect(route('orders.success', $order->order_code));

        $this->get(route('orders.success', $order->order_code))
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertDontSee('081234000999')
            ->assertDontSee('Jl. Privasi Blok Z No. 7');

        $this->get(route('orders.track', $order->order_code))
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Express')
            ->assertSee('Menunggu dijemput')
            ->assertDontSee('Rahasia Uji Blade')
            ->assertDontSee('081234000999')
            ->assertDontSee('Jl. Privasi Blok Z No. 7');
    }

    public function test_staff_can_view_orders_services_and_daily_revenue_blade_pages(): void
    {
        $staff = User::factory()->create();
        $reguler = Service::where('code', 'REGULER')->firstOrFail();

        $order = Order::factory()->create([
            'service_id' => $reguler->id,
            'status' => OrderStatus::Delivered,
            'actual_weight_grams' => 2500,
            'billed_weight_kg' => '2.50',
            'applied_unit_price' => 7000,
            'applied_surcharge_percent' => 0,
            'final_amount' => 17500,
            'weighed_at' => CarbonImmutable::parse('2026-11-21 08:00:00', 'Asia/Jakarta')->utc(),
            'estimated_completion_at' => CarbonImmutable::parse('2026-11-24 08:00:00', 'Asia/Jakarta')->utc(),
            'delivered_at' => CarbonImmutable::parse('2026-11-21 15:00:00', 'Asia/Jakarta')->utc(),
            'payment_status' => PaymentStatus::Paid,
            'paid_at' => CarbonImmutable::parse('2026-11-21 14:30:00', 'Asia/Jakarta')->utc(),
        ]);

        $this->get('/login')->assertOk()->assertSee('Login Petugas');

        $this->actingAs($staff)
            ->get('/staff/orders')
            ->assertOk()
            ->assertSee($order->order_code);

        $this->actingAs($staff)
            ->get("/staff/orders/{$order->id}")
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Order sudah diserahkan. Tidak bisa diubah lagi.');

        $this->actingAs($staff)
            ->get('/staff/services')
            ->assertOk()
            ->assertSee('Reguler')
            ->assertSee('Express');

        $this->actingAs($staff)
            ->get('/staff/reports/daily-revenue?date=2026-11-21')
            ->assertOk()
            ->assertSee('Laporan Pendapatan Harian')
            ->assertSee('Rp 17.500')
            ->assertSee($order->order_code);
    }
}
