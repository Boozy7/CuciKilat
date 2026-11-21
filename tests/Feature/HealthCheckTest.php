<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_returns_ok_when_database_is_reachable(): void
    {
        $response = $this->getJson('/health');

        $response
            ->assertOk()
            ->assertExactJson([
                'status' => 'ok',
                'database' => 'ok',
            ]);
    }

    public function test_health_returns_service_unavailable_without_sensitive_details_when_database_fails(): void
    {
        DB::partialMock()
            ->shouldReceive('select')
            ->once()
            ->with('SELECT 1')
            ->andThrow(new RuntimeException('SQLSTATE[HY000] [1045] Access denied for user secret_user'));

        $response = $this->getJson('/health');

        $response
            ->assertStatus(503)
            ->assertExactJson([
                'status' => 'error',
                'database' => 'unavailable',
            ]);

        $this->assertStringNotContainsString('secret_user', $response->getContent());
    }
}
