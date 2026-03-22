<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Contracte dels endpoints públics d'infraestructura que es mantenen oberts.
 */
class ApiPublicInfrastructureEndpointsFeatureTest extends TestCase
{
    public function test_mi_ip_continua_sent_public(): void
    {
        $response = $this->getJson('/api/miIp');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertIsString($response->json('data'));
    }

    public function test_server_time_continua_sent_public(): void
    {
        $response = $this->getJson('/api/server-time');

        $response->assertOk();
        $response->assertJsonStructure(['date', 'time']);
    }
}
