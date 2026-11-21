<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ServiceSeeder::class);
        $this->staff = User::factory()->create();
    }

    public function test_customer_can_create_pickup_order_with_null_billing_and_unique_code(): void
    {
        $reguler = Service::where('code', 'REGULER')->firstOrFail();

        $response = $this->postJson('/orders', [
            'service_id' => $reguler->id,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'pickup_address' => 'Kos Mawar No. 12, Ketintang',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', OrderStatus::WaitingPickup->value)
            ->assertJsonPath('data.payment_status', PaymentStatus::Unpaid->value)
            ->assertJsonPath('data.billed_weight_kg', null)
            ->assertJsonPath('data.final_amount', null)
            ->assertJsonPath('data.estimated_completion_at', null);

        $orderCode = $response->json('data.order_code');
        $this->assertNotEmpty($orderCode);

        $order = Order::where('order_code', $orderCode)->firstOrFail();
        $this->assertSame(OrderStatus::WaitingPickup, $order->status);
        $this->assertNull($order->actual_weight_grams);
        $this->assertNull($order->billed_weight_kg);
        $this->assertNull($order->final_amount);
        $this->assertNull($order->estimated_completion_at);
    }

    public function test_customer_cannot_create_order_with_inactive_service(): void
    {
        $inactiveService = Service::factory()->inactive()->create();

        $response = $this->postJson('/orders', [
            'service_id' => $inactiveService->id,
            'customer_name' => 'Ani Wijaya',
            'customer_phone' => '081299998888',
            'pickup_address' => 'Jl. Ahmad Yani No. 5',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['service_id']);
    }

    public function test_express_order_lifecycle_calculates_surcharge_estimate_and_locks_after_delivered(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-21 09:00:00', 'Asia/Jakarta'));

        try {
            $express = Service::where('code', 'EXPRESS')->firstOrFail();

            // 1. Pelanggan buat order jemput
            $createResponse = $this->postJson('/orders', [
                'service_id' => $express->id,
                'customer_name' => 'Siti Rahma',
                'customer_phone' => '081355556666',
                'pickup_address' => 'Kos Melati Blok B3',
            ]);
            $createResponse->assertCreated();

            $order = Order::where('order_code', $createResponse->json('data.order_code'))->firstOrFail();

            // 2. Petugas tandai picked_up
            $this->actingAs($this->staff)
                ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'picked_up'])
                ->assertOk()
                ->assertJsonPath('data.status', 'picked_up');

            // 3. Petugas timbang 2,51 kg (2510 gram) -> 3,00 kg * Rp7.000 * 150% = Rp31.500, estimasi +1 hari
            $this->actingAs($this->staff)
                ->postJson("/staff/orders/{$order->id}/weigh", ['actual_weight_grams' => 2510])
                ->assertOk()
                ->assertJsonPath('data.actual_weight_grams', 2510)
                ->assertJsonPath('data.billed_weight_kg', 3)
                ->assertJsonPath('data.applied_unit_price', 7000)
                ->assertJsonPath('data.applied_surcharge_percent', 50)
                ->assertJsonPath('data.final_amount', 31500);

            $order->refresh();
            $this->assertTrue(
                $order->estimated_completion_at->equalTo(Carbon::parse('2026-11-22 09:00:00', 'Asia/Jakarta'))
            );

            // 4. Lanjut status berurutan: processing -> ready_for_delivery -> bayar -> delivered
            $this->actingAs($this->staff)
                ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'processing'])
                ->assertOk();

            $this->actingAs($this->staff)
                ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'ready_for_delivery'])
                ->assertOk();

            $this->actingAs($this->staff)
                ->patchJson("/staff/orders/{$order->id}/payment", ['payment_status' => 'paid'])
                ->assertOk()
                ->assertJsonPath('data.payment_status', 'paid');

            $this->actingAs($this->staff)
                ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'delivered'])
                ->assertOk()
                ->assertJsonPath('data.status', 'delivered');

            $order->refresh();
            $this->assertNotNull($order->delivered_at);

            // 5. Setelah delivered, semua perubahan order ditolak di backend
            $this->actingAs($this->staff)
                ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'processing'])
                ->assertUnprocessable();

            $this->actingAs($this->staff)
                ->postJson("/staff/orders/{$order->id}/weigh", ['actual_weight_grams' => 3000])
                ->assertUnprocessable();

            $this->actingAs($this->staff)
                ->patchJson("/staff/orders/{$order->id}/payment", ['payment_status' => 'unpaid'])
                ->assertUnprocessable();

            $this->expectException(ValidationException::class);
            $order->update(['customer_name' => 'Perubahan Ilegal']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_status_must_progress_sequentially_and_require_weighing_before_processing(): void
    {
        $reguler = Service::where('code', 'REGULER')->firstOrFail();
        $order = Order::factory()->create([
            'service_id' => $reguler->id,
            'status' => OrderStatus::WaitingPickup,
        ]);

        // Tidak boleh lompat dari waiting_pickup ke processing
        $this->actingAs($this->staff)
            ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'processing'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);

        // Tidak boleh menimbang saat masih waiting_pickup
        $this->actingAs($this->staff)
            ->postJson("/staff/orders/{$order->id}/weigh", ['actual_weight_grams' => 2000])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);

        // Masuk picked_up
        $this->actingAs($this->staff)
            ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'picked_up'])
            ->assertOk();

        // Tidak boleh masuk processing sebelum ditimbang
        $this->actingAs($this->staff)
            ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'processing'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);

        // Timbang 2,50 kg (2500 gram) -> billed 2,50 kg, Rp17.500
        $this->actingAs($this->staff)
            ->postJson("/staff/orders/{$order->id}/weigh", ['actual_weight_grams' => 2500])
            ->assertOk()
            ->assertJsonPath('data.billed_weight_kg', 2.5)
            ->assertJsonPath('data.final_amount', 17500);

        // Sekarang boleh masuk processing, tapi tidak boleh lompat langsung ke delivered
        $this->actingAs($this->staff)
            ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'delivered'])
            ->assertUnprocessable();

        $this->actingAs($this->staff)
            ->patchJson("/staff/orders/{$order->id}/status", ['status' => 'processing'])
            ->assertOk();
    }

    public function test_master_service_price_update_only_affects_unbilled_orders(): void
    {
        $reguler = Service::where('code', 'REGULER')->firstOrFail();

        $billedOrder = Order::factory()->create([
            'service_id' => $reguler->id,
            'status' => OrderStatus::PickedUp,
        ]);
        $unbilledOrder = Order::factory()->create([
            'service_id' => $reguler->id,
            'status' => OrderStatus::PickedUp,
        ]);

        // Timbang order pertama saat tarif masih Rp7.000/kg -> 2 kg = Rp14.000
        $this->actingAs($this->staff)
            ->postJson("/staff/orders/{$billedOrder->id}/weigh", ['actual_weight_grams' => 1200])
            ->assertOk()
            ->assertJsonPath('data.final_amount', 14000);

        // Admin naikkan tarif master Reguler jadi Rp9.000/kg
        $this->actingAs($this->staff)
            ->putJson("/staff/services/{$reguler->id}", ['base_price' => 9000])
            ->assertOk();

        // Order pertama tetap memakai snapshot lama Rp7.000/kg -> Rp14.000
        $billedOrder->refresh();
        $this->assertSame(7000, $billedOrder->applied_unit_price);
        $this->assertSame(14000, $billedOrder->final_amount);

        // Order kedua yang belum ditagihkan memakai tarif baru Rp9.000/kg -> 2 kg = Rp18.000
        $this->actingAs($this->staff)
            ->postJson("/staff/orders/{$unbilledOrder->id}/weigh", ['actual_weight_grams' => 1200])
            ->assertOk()
            ->assertJsonPath('data.applied_unit_price', 9000)
            ->assertJsonPath('data.final_amount', 18000);
    }

    public function test_satuan_order_uses_item_count_and_rejects_weight_input(): void
    {
        $satuan = Service::where('code', 'SATUAN')->firstOrFail();
        $order = Order::factory()->create([
            'service_id' => $satuan->id,
            'status' => OrderStatus::PickedUp,
        ]);

        $this->actingAs($this->staff)
            ->postJson("/staff/orders/{$order->id}/weigh", ['actual_weight_grams' => 2000])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['item_count', 'actual_weight_grams']);

        $this->actingAs($this->staff)
            ->postJson("/staff/orders/{$order->id}/weigh", ['item_count' => 3])
            ->assertOk()
            ->assertJsonPath('data.item_count', 3)
            ->assertJsonPath('data.billed_weight_kg', null)
            ->assertJsonPath('data.final_amount', 45000);
    }

    public function test_public_tracking_returns_order_status_and_billing_without_pii(): void
    {
        $reguler = Service::where('code', 'REGULER')->firstOrFail();
        $order = Order::factory()->create([
            'order_code' => 'CK-20261121-ABC123',
            'service_id' => $reguler->id,
            'customer_name' => 'Rahasia Pelanggan',
            'customer_phone' => '081999888777',
            'pickup_address' => 'Jl. Rahasia Kos No. 99',
            'status' => OrderStatus::PickedUp,
        ]);

        // Sebelum ditimbang: tagihan dan estimasi kosong, tanpa PII
        $beforeResponse = $this->getJson('/track/ck-20261121-abc123');
        $beforeResponse
            ->assertOk()
            ->assertJsonPath('data.order_code', 'CK-20261121-ABC123')
            ->assertJsonPath('data.service.name', 'Reguler')
            ->assertJsonPath('data.status', 'picked_up')
            ->assertJsonPath('data.final_amount', null)
            ->assertJsonPath('data.estimated_completion_at', null)
            ->assertJsonMissingPath('data.customer_name')
            ->assertJsonMissingPath('data.customer_phone')
            ->assertJsonMissingPath('data.pickup_address');

        $rawContent = $beforeResponse->getContent();
        $this->assertStringNotContainsString('Rahasia Pelanggan', $rawContent);
        $this->assertStringNotContainsString('081999888777', $rawContent);
        $this->assertStringNotContainsString('Jl. Rahasia Kos No. 99', $rawContent);

        // Setelah ditimbang: tampilkan tagihan & estimasi, tetap tanpa PII
        $this->actingAs($this->staff)
            ->postJson("/staff/orders/{$order->id}/weigh", ['actual_weight_grams' => 2500])
            ->assertOk();

        $afterResponse = $this->getJson('/track/CK-20261121-ABC123');
        $afterResponse
            ->assertOk()
            ->assertJsonPath('data.billed_weight_kg', 2.5)
            ->assertJsonPath('data.final_amount', 17500);

        $this->assertNotNull($afterResponse->json('data.estimated_completion_at'));
        $this->assertStringNotContainsString('081999888777', $afterResponse->getContent());
        $this->assertStringNotContainsString('Jl. Rahasia Kos No. 99', $afterResponse->getContent());

        // Kode tidak dikenal -> 404
        $this->getJson('/track/CK-TIDAK-ADA')->assertNotFound();
    }
}
