<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Intranet\Entities\CotxeAcces;
use Intranet\Services\CotxeAccessService;
use Mockery;
use Tests\TestCase;

class CotxeAccessServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_recent_access_within_returns_false_when_no_records()
    {
        $cotxeAcces = Mockery::mock('alias:' . CotxeAcces::class);
        $cotxeAcces->shouldReceive('where')
            ->with('matricula', '1234ABC')
            ->andReturnSelf();
        $cotxeAcces->shouldReceive('latest')
            ->with('created_at')
            ->andReturnSelf();
        $cotxeAcces->shouldReceive('first')
            ->andReturn(null);

        $service = new CotxeAccessService();

        $this->assertFalse($service->recentAccessWithin('1234ABC', 30));
    }

    public function test_recent_access_within_detects_recent_entry()
    {
        $cotxeAcces = Mockery::mock('alias:' . CotxeAcces::class);
        $cotxeAcces->shouldReceive('where')
            ->with('matricula', '1234ABC')
            ->andReturnSelf();
        $cotxeAcces->shouldReceive('latest')
            ->with('created_at')
            ->andReturnSelf();
        $cotxeAcces->shouldReceive('first')
            ->andReturn((object)['created_at' => now()->subSeconds(10)]);

        $service = new CotxeAccessService();

        $this->assertTrue($service->recentAccessWithin('1234ABC', 30));
    }

    public function test_registrar_acces_crea_registre()
    {
        $cotxeAcces = Mockery::mock('alias:' . CotxeAcces::class);
        $cotxeAcces->shouldReceive('create')
            ->once()
            ->with([
                'matricula' => '1234ABC',
                'autoritzat' => true,
                'porta_oberta' => false,
                'device' => 'device-1',
                'tipus' => 'manual',
            ]);

        $service = new CotxeAccessService();
        $service->registrarAcces('1234ABC', true, false, 'device-1', 'manual');

        $this->addToAssertionCount(1);
    }

    public function test_obrir_i_porta_envia_les_ordres_al_dispositiu()
    {
        config([
            'parking.porta_url' => 'http://porta.test',
            'parking.porta_device_id' => 'device-1',
            'parking.porta_user' => 'usuari',
            'parking.porta_pass' => 'clau',
        ]);

        Http::fake([
            'http://porta.test/api/callAction*' => Http::response('OK', 200),
        ]);

        $service = new CotxeAccessService();

        $this->assertTrue($service->obrirIPorta());

        Http::assertSentCount(2);
        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'http://porta.test/api/callAction')
                && $request['deviceID'] === 'device-1'
                && in_array($request['name'], ['turnOn', 'turnOff'], true);
        });
    }
}
