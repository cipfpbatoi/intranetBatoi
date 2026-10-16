<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Reunion\ReunionArchiveDocumentService;
use Intranet\Application\Reunion\ReunionArchivePdfService;
use Intranet\Application\Reunion\ReunionArchiveService;
use Intranet\Application\Reunion\ReunionContinuityService;
use Intranet\Entities\Reunion;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * Proves del cas d'ús transaccional d'arxivament d'actes.
 */
class ReunionArchiveServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Event::fake();

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('ordenes_reuniones');
        $schema->dropIfExists('reuniones');

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('idGrupo', 10)->nullable();
            $table->unsignedTinyInteger('tipo')->default(2);
            $table->boolean('archivada')->default(false);
            $table->string('fichero')->nullable();
            $table->timestamps();
        });

        $schema->create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->unsignedTinyInteger('orden')->default(1);
            $table->string('descripcion')->nullable();
            $table->text('resumen')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_arxiva_l_acta_en_una_unica_operacio(): void
    {
        $reunion = $this->insertAct(1, false);
        $this->insertOrder(1, null);
        $path = storage_path('app/gestor/2026-2027/Reunion/Acta_1.pdf');

        $continuity = Mockery::mock(ReunionContinuityService::class);
        $continuity->shouldReceive('normaliseEmptySummaries')
            ->once()
            ->with($reunion)
            ->andReturnUsing(function (): void {
                DB::table('ordenes_reuniones')->where('idReunion', 1)->update([
                    'resumen' => ReunionContinuityService::DEFAULT_SUMMARY,
                ]);
            });

        $pdf = Mockery::mock(ReunionArchivePdfService::class);
        $pdf->shouldReceive('save')->once()->with($reunion, $path);

        $documents = Mockery::mock(ReunionArchiveDocumentService::class);
        $documents->shouldReceive('save')
            ->once()
            ->withArgs(function (Reunion $savedReunion, string $course): bool {
                return $savedReunion->archivada
                    && $savedReunion->fichero === 'gestor/2026-2027/Reunion/Acta_1.pdf'
                    && $course === '2026-2027';
            });

        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('exists')->once()->with($path)->andReturnFalse();

        $result = $this->service($continuity, $pdf, $documents, $filesystem)
            ->archive($reunion, '2026-2027');

        $this->assertFalse($result->wasAlreadyArchived());
        $this->assertDatabaseHas('reuniones', [
            'id' => 1,
            'archivada' => 1,
            'fichero' => 'gestor/2026-2027/Reunion/Acta_1.pdf',
        ]);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => 1,
            'resumen' => ReunionContinuityService::DEFAULT_SUMMARY,
        ]);
    }

    public function test_revertix_la_base_de_dades_i_elimina_el_pdf_parcial_si_falla_el_gestor(): void
    {
        $reunion = $this->insertAct(2, false);
        $this->insertOrder(2, null);
        $path = storage_path('app/gestor/2026-2027/Reunion/Acta_2.pdf');

        $continuity = Mockery::mock(ReunionContinuityService::class);
        $continuity->shouldReceive('normaliseEmptySummaries')
            ->once()
            ->andReturnUsing(function (): void {
                DB::table('ordenes_reuniones')->where('idReunion', 2)->update([
                    'resumen' => ReunionContinuityService::DEFAULT_SUMMARY,
                ]);
            });

        $pdf = Mockery::mock(ReunionArchivePdfService::class);
        $pdf->shouldReceive('save')->once()->with($reunion, $path);

        $documents = Mockery::mock(ReunionArchiveDocumentService::class);
        $documents->shouldReceive('save')
            ->once()
            ->andThrow(new RuntimeException('Error simulat del gestor documental'));

        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('exists')->once()->with($path)->andReturnFalse();
        $filesystem->shouldReceive('isFile')->once()->with($path)->andReturnTrue();
        $filesystem->shouldReceive('delete')->once()->with($path)->andReturnTrue();

        try {
            $this->service($continuity, $pdf, $documents, $filesystem)
                ->archive($reunion, '2026-2027');
            $this->fail("S'esperava l'error del gestor documental.");
        } catch (RuntimeException $exception) {
            $this->assertSame('Error simulat del gestor documental', $exception->getMessage());
        }

        $this->assertDatabaseHas('reuniones', ['id' => 2, 'archivada' => 0, 'fichero' => null]);
        $this->assertDatabaseHas('ordenes_reuniones', ['idReunion' => 2, 'resumen' => null]);
    }

    public function test_no_modifica_ni_regenera_una_acta_ja_arxivada(): void
    {
        $reunion = $this->insertAct(3, true, 'gestor/2026-2027/Reunion/Acta_3.pdf');

        $continuity = Mockery::mock(ReunionContinuityService::class);
        $continuity->shouldNotReceive('normaliseEmptySummaries');
        $pdf = Mockery::mock(ReunionArchivePdfService::class);
        $pdf->shouldNotReceive('save');
        $documents = Mockery::mock(ReunionArchiveDocumentService::class);
        $documents->shouldNotReceive('save');
        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldNotReceive('exists', 'isFile', 'delete');

        $result = $this->service($continuity, $pdf, $documents, $filesystem)
            ->archive($reunion, '2026-2027');

        $this->assertTrue($result->wasAlreadyArchived());
        $this->assertDatabaseHas('reuniones', [
            'id' => 3,
            'archivada' => 1,
            'fichero' => 'gestor/2026-2027/Reunion/Acta_3.pdf',
        ]);
    }

    /**
     * Crea el servei amb dependències substituïdes per dobles de prova.
     */
    private function service(
        ReunionContinuityService $continuity,
        ReunionArchivePdfService $pdf,
        ReunionArchiveDocumentService $documents,
        Filesystem $filesystem
    ): ReunionArchiveService {
        return new ReunionArchiveService($continuity, $pdf, $documents, $filesystem);
    }

    /**
     * Inserix i retorna una reunió mínima per a les proves.
     */
    private function insertAct(int $id, bool $archived, ?string $file = null): Reunion
    {
        DB::table('reuniones')->insert([
            'id' => $id,
            'idProfesor' => 'P1',
            'idGrupo' => null,
            'tipo' => 2,
            'archivada' => $archived,
            'fichero' => $file,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Reunion::query()->findOrFail($id);
    }

    /**
     * Inserix un punt mínim associat a l'acta.
     */
    private function insertOrder(int $act, ?string $summary): void
    {
        DB::table('ordenes_reuniones')->insert([
            'idReunion' => $act,
            'orden' => 1,
            'descripcion' => 'Observacions',
            'resumen' => $summary,
        ]);
    }
}
