<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Intranet\Entities\CotxeAcces;
use Intranet\Services\School\CotxeAccessService;
use Mockery;
use Tests\TestCase;

class CotxeAccessServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_reintenta_una_vegada_quan_la_primera_ordre_falla(): void
    {
        $service = Mockery::mock(CotxeAccessService::class)->makePartial();
        $service->shouldReceive('obrirIPorta')->twice()->andReturn(false, true);

        $this->assertSame([
            'porta_oberta' => true,
            'intents' => 2,
            'error' => null,
        ], $service->obrirIPortaAmbReintent());
    }

    public function test_registra_fallada_despres_de_dos_intents(): void
    {
        $service = Mockery::mock(CotxeAccessService::class)->makePartial();
        $service->shouldReceive('obrirIPorta')->twice()->andReturnFalse();

        $this->assertSame([
            'porta_oberta' => false,
            'intents' => 2,
            'error' => 'sense_resposta_satisfactoria',
        ], $service->obrirIPortaAmbReintent());
    }

    public function test_registrar_acces_crea_registre()
    {
        $cotxeAcces = Mockery::mock('alias:' . CotxeAcces::class);
        $cotxeAcces->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $valors): bool {
                return $valors['matricula'] === '1234ABC'
                    && $valors['autoritzat'] === true
                    && $valors['porta_oberta'] === false
                    && $valors['device'] === 'device-1'
                    && $valors['tipus'] === 'manual'
                    && $valors['intents_domotica'] === 0;
            }));

        $service = new CotxeAccessService();
        $service->registrarAcces('1234ABC', true, false, 'device-1', 'manual');

        $this->addToAssertionCount(1);
    }

    public function test_obrir_i_porta_envia_les_ordres_al_dispositiu()
    {
        config([
            'logging.channels.parking' => ['driver' => 'errorlog', 'level' => 'debug'],
            'variables.domotica.host' => 'http://domotica.test',
            'variables.domotica.user' => 'usuari',
            'variables.domotica.pass' => 'clau',
            'variables.domotica.openSceneId' => 111,
        ]);

        Http::fake([
            'http://domotica.test/api/scenes/111/execute' => Http::response('OK', 200),
        ]);

        $service = new CotxeAccessService();

        $this->assertTrue($service->obrirIPorta());

        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            return $request->url() === 'http://domotica.test/api/scenes/111/execute'
                && $request->method() === 'POST';
        });
    }
}
