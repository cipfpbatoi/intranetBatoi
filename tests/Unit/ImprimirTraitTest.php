<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Http\RedirectResponse;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\Document\PdfService;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ImprimirTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_ics_retorna_calendari_quan_els_camps_existixen(): void
    {
        DummyImprimirModel::$record = (object) [
            'descripcion' => 'Reunio de prova',
            'objetivos' => 'Revisar incidencies',
            'fecha' => '2026-02-10 10:00:00',
        ];

        $controller = new DummyImprimirController();
        $response = $controller->ics(12);

        $this->assertSame('text/calendar', (string) $response->headers->get('Content-Type'));
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('BEGIN:VCALENDAR', $content);
    }

    public function test_ics_torna_back_amb_error_si_falten_camps(): void
    {
        $this->startSession();

        DummyImprimirModel::$record = (object) [
            'fecha' => '2026-02-10 10:00:00',
        ];

        $controller = new DummyImprimirController();
        $response = $controller->ics(12, 'descripcion', 'objetivos');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('No existeixen els camps', (string) $response->getSession()->get('error'));
    }

    public function test_ics_fa_abort_si_falta_class_en_controller(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("L'atribut 'class' no està definit");

        DummyImprimirModel::$record = (object) [
            'descripcion' => 'x',
            'objetivos' => 'y',
            'fecha' => '2026-02-10 10:00:00',
        ];

        $controller = new DummyImprimirWithoutClassController();
        $controller->ics(1);
    }

    public function test_hazpdf_delega_en_pdfservice(): void
    {
        $pdf = new \stdClass();

        $service = Mockery::mock(PdfService::class);
        $service->shouldReceive('hazPdf')
            ->once()
            ->with('pdf.demo', ['a' => 1], ['meta' => 1], 'landscape', 'a4', 10)
            ->andReturn($pdf);

        $this->app->instance(PdfService::class, $service);

        $result = DummyImprimirController::callHazPdf('pdf.demo', ['a' => 1], ['meta' => 1], 'landscape', 'a4', 10);

        $this->assertSame($pdf, $result);
    }

    public function test_gestor_fa_abort_si_falta_class_en_controller(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("L'atribut 'class' no està definit");

        $controller = new DummyImprimirWithoutClassController();
        $controller->gestor(1);
    }
}

class DummyImprimirModel
{
    public static object $record;

    public static function findOrFail(int $id): object
    {
        return self::$record;
    }
}

class DummyImprimirController
{
    use Imprimir;

    public string $class = DummyImprimirModel::class;

    public static function callHazPdf(
        string $informe,
        mixed $todos,
        mixed $datosInforme = null,
        string $orientacion = 'portrait',
        string|array $dimensiones = 'a4',
        int $marginTop = 15
    ): mixed {
        return self::hazPdf($informe, $todos, $datosInforme, $orientacion, $dimensiones, $marginTop);
    }
}

class DummyImprimirWithoutClassController
{
    use Imprimir;
}
