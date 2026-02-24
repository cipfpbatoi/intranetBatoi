<?php

declare(strict_types=1);

namespace Tests\Unit\Application\FaltaItaca;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\FaltaItaca\FaltaItacaWorkflowService;
use Intranet\Entities\Documento;
use Tests\TestCase;

class FaltaItacaWorkflowServiceTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
        });

        $schema->create('faltas_itaca', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->date('dia');
            $table->unsignedTinyInteger('estado')->default(1);
        });

        $schema->create('documentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipoDocumento')->nullable();
            $table->string('fichero')->nullable();
            $table->timestamps();
        });
    }

    public function test_find_elements_filtra_per_estat_i_rang(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P001',
            'nombre' => 'Ada',
            'apellido1' => 'Alba',
            'apellido2' => 'Boi',
        ]);

        DB::table('faltas_itaca')->insert([
            ['idProfesor' => 'P001', 'dia' => '2026-02-10', 'estado' => 2],
            ['idProfesor' => 'P001', 'dia' => '2026-02-11', 'estado' => 1],
            ['idProfesor' => 'P001', 'dia' => '2026-03-01', 'estado' => 2],
        ]);

        $service = new FaltaItacaWorkflowService();
        $items = $service->findElements('2026-02-01', '2026-02-28');

        $this->assertCount(1, $items);
        $this->assertSame('10-02-2026', $items->first()->dia);
        $this->assertSame(2, (int) $items->first()->estado);
    }

    public function test_delete_previous_monthly_report_esborra_fitxer_i_document(): void
    {
        $relative = 'testing/falta_itaca/report.pdf';
        $absolute = storage_path('app/' . $relative);

        $dir = dirname($absolute);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($absolute, 'pdf');

        $doc = Documento::query()->create(['fichero' => $relative]);

        $service = new FaltaItacaWorkflowService();
        $service->deletePreviousMonthlyReport($relative);

        $this->assertFalse(file_exists($absolute));
        $this->assertNull(Documento::query()->find($doc->id));
    }

    public function test_resolve_i_refuse_retorn_false_si_no_troba_absencia(): void
    {
        $service = new FaltaItacaWorkflowService();

        $this->assertFalse($service->resolveByAbsenceId(99999));
        $this->assertFalse($service->refuseByAbsenceId(99999, 'test'));
    }

    public function test_monthly_report_file_name_te_format_esperat(): void
    {
        $service = new FaltaItacaWorkflowService();

        $name = $service->monthlyReportFileName('2026-02-15');

        $this->assertStringStartsWith('gestor/', $name);
        $this->assertStringContainsString('/informes/Birret', $name);
        $this->assertStringEndsWith('.pdf', $name);
    }
}
