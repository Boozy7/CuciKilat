<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create();
    }

    public function test_unauthenticated_requests_to_staff_endpoints_are_rejected(): void
    {
        $this->getJson('/staff/orders')->assertUnauthorized();
        $this->getJson('/staff/services')->assertUnauthorized();
        $this->getJson('/staff/reports/daily-revenue')->assertUnauthorized();

        $this->get('/staff/services')->assertRedirect('/login');
    }

    public function test_staff_can_crud_unused_service(): void
    {
        $createResponse = $this->actingAs($this->staff)->postJson('/staff/services', [
            'code' => 'kilat_malam',
            'name' => 'Kilat Malam',
            'pricing_type' => 'per_kg',
            'base_price' => 8000,
            'surcharge_percent' => 25,
            'estimated_days' => 1,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.code', 'KILAT_MALAM')
            ->assertJsonPath('data.is_active', true);

        $serviceId = $createResponse->json('data.id');

        $this->actingAs($this->staff)
            ->getJson("/staff/services/{$serviceId}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Kilat Malam');

        $this->actingAs($this->staff)
            ->putJson("/staff/services/{$serviceId}", [
                'name' => 'Kilat Malam Super',
                'base_price' => 8500,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Kilat Malam Super')
            ->assertJsonPath('data.base_price', 8500);

        $this->actingAs($this->staff)
            ->deleteJson("/staff/services/{$serviceId}")
            ->assertOk()
            ->assertJsonPath('action', 'deleted');

        $this->assertDatabaseMissing('services', ['id' => $serviceId]);
    }

    public function test_deleting_service_used_by_orders_deactivates_it_instead_of_deleting(): void
    {
        $service = Service::factory()->create(['is_active' => true]);
        $order = Order::factory()->create(['service_id' => $service->id]);

        $this->actingAs($this->staff)
            ->deleteJson("/staff/services/{$service->id}")
            ->assertOk()
            ->assertJsonPath('action', 'deactivated')
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        // Layanan nonaktif tidak tampil di daftar publik
        $this->getJson('/services')
            ->assertOk()
            ->assertJsonMissing(['id' => $service->id]);
    }
}
