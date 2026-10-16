<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Reunion\ReunionArchivePdfService;
use Intranet\Entities\Reunion;
use Intranet\Services\Document\PdfService;
use Mockery;
use Tests\TestCase;

/**
 * Proves de la preparació del model de vista per al PDF d'una acta.
 */
class ReunionArchivePdfServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_les_dades_de_presentacio_no_contaminen_el_model_que_es_persistira(): void
    {
        $reunion = new Reunion();
        $reunion->forceFill([
            'id' => 5629,
            'tipo' => 2,
            'fecha' => '2026-09-23 12:00:00',
            'updated_at' => '2026-09-29 16:21:33',
        ]);
        $reunion->syncOriginal();

        $generatedPdf = Mockery::mock();
        $generatedPdf->shouldReceive('save')->once()->with('/tmp/Acta_5629.pdf');

        $pdfService = Mockery::mock(PdfService::class);
        $pdfService->shouldReceive('hazPdf')
            ->once()
            ->withArgs(function (
                string $view,
                mixed $orders,
                Reunion $viewData,
                string $orientation,
                string $paper
            ) use ($reunion): bool {
                return $viewData !== $reunion
                    && isset($viewData->dia, $viewData->hora, $viewData->hoy)
                    && $orientation === 'portrait'
                    && $paper === 'a4';
            })
            ->andReturn($generatedPdf);

        (new ReunionArchivePdfService($pdfService))->save($reunion, '/tmp/Acta_5629.pdf');

        $this->assertArrayNotHasKey('dia', $reunion->getAttributes());
        $this->assertArrayNotHasKey('hora', $reunion->getAttributes());
        $this->assertArrayNotHasKey('hoy', $reunion->getAttributes());
        $this->assertSame([], $reunion->getDirty());
    }
}
