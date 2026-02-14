<?php

declare(strict_types=1);

namespace Tests\Unit;

use Carbon\CarbonImmutable;
use Tests\TestCase;

class MyHelpersTest extends TestCase
{
    private array $serverBackup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER;
        config(['roles.rol' => [
            'administrador' => 11,
            'direccion' => 2,
            'tutor' => 3,
        ]]);
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    public function test_esrol_torna_true_quan_rol_esta_inclos(): void
    {
        $this->assertTrue(esRol(66, 11));
    }

    public function test_esrol_torna_false_quan_rol_no_esta_inclos(): void
    {
        $this->assertFalse(esRol(6, 11));
    }

    public function test_hazarray_construeix_clau_i_valor_simple(): void
    {
        $items = [
            (object) ['id' => 10, 'name' => 'A'],
            (object) ['id' => 20, 'name' => 'B'],
        ];

        $this->assertSame([10 => 'A', 20 => 'B'], hazArray($items, 'id', 'name'));
    }

    public function test_hazarray_admet_propietat_anidada(): void
    {
        $items = [
            (object) ['id' => 1, 'owner' => (object) ['name' => 'Ignasi']],
        ];

        $this->assertSame([1 => 'Ignasi'], hazArray($items, 'id', 'owner->name'));
    }

    public function test_in_substr_retalla_i_afegix_punts_suspensius(): void
    {
        $this->assertSame('abcdef…', in_substr('abcdefghi', 6));
    }

    public function test_in_substr_no_retalla_si_es_curta(): void
    {
        $this->assertSame('abc', in_substr('abc', 6));
    }

    public function test_in_substr_normalitza_bool_i_dates(): void
    {
        $this->assertSame('Sí', in_substr(true, 10));
        $this->assertSame('No', in_substr(false, 10));
        $this->assertSame('2026-02-10 12:00:00', in_substr(CarbonImmutable::parse('2026-02-10 12:00:00'), 30));
    }

    public function test_getclientipaddress_prioritza_http_client_ip(): void
    {
        $_SERVER = [];
        $_SERVER['HTTP_CLIENT_IP'] = '1.1.1.1';
        $_SERVER['REMOTE_ADDR'] = '2.2.2.2';

        $this->assertSame('1.1.1.1', getClientIpAddress());
    }

    public function test_getclientipaddress_fallback_remote_addr(): void
    {
        $_SERVER = [];
        $_SERVER['REMOTE_ADDR'] = '2.2.2.2';

        $this->assertSame('2.2.2.2', getClientIpAddress());
    }

    public function test_isprivateaddress_detecta_privades_i_publiques(): void
    {
        $this->assertTrue(isPrivateAddress('192.168.1.20'));
        $this->assertFalse(isPrivateAddress('8.8.8.8'));
    }
}

