<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Reunion\ReunionContinuityService;
use Intranet\Entities\Reunion;
use Intranet\Services\Calendar\MeetingOrderGenerateService;
use Tests\TestCase;

/**
 * Proves de continuïtat i normalització dels punts de les actes.
 */
class ReunionContinuityServiceTest extends TestCase
{
    use WithoutModelEvents;

    private ReunionContinuityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('ordenes_reuniones');
        $schema->dropIfExists('reuniones');
        $schema->dropIfExists('grupos');

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('tutor', 10)->nullable();
            $table->timestamps();
        });

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo')->default(2);
            $table->string('idGrupo', 10)->nullable();
            $table->string('idProfesor', 10);
            $table->string('curso');
            $table->dateTime('fecha');
            $table->boolean('archivada')->default(false);
            $table->timestamps();
        });

        $schema->create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->unsignedTinyInteger('orden')->default(1);
            $table->string('descripcion', 120)->nullable();
            $table->text('resumen')->nullable();
        });

        DB::table('grupos')->insert([
            [
                'codigo' => 'G1',
                'nombre' => 'Grup 1',
                'tutor' => 'P1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'G2',
                'nombre' => 'Grup 2',
                'tutor' => 'P2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->service = new ReunionContinuityService();
    }

    public function test_recupera_acords_i_seguiment_nese_de_ultima_acta_arxivada(): void
    {
        $oldAct = $this->insertAct(1, '2026-09-01 10:00:00', true);
        $previousAct = $this->insertAct(2, '2026-09-15 10:00:00', true);
        $this->insertAct(3, '2026-09-20 10:00:00', false);
        $currentAct = $this->insertAct(4, '2026-09-22 10:00:00', false);
        $otherGroupAct = $this->insertAct(5, '2026-09-21 10:00:00', true, 'G2', 'P2');
        $workGroupAct = $this->insertAct(6, '2026-09-21 12:00:00', true, null, 'P1', 3);

        $this->insertOrder($oldAct, 'Acords adoptats', 'Acord antic');
        $this->insertOrder($previousAct, 'Acords adoptats', '<p>Acord vigent</p>');
        $this->insertOrder($otherGroupAct, 'Acords adoptats', 'Acord d\'un altre grup');
        $this->insertOrder($workGroupAct, 'Acords adoptats', 'Acord de grup de treball');
        $this->insertOrder(
            $previousAct,
            'Alumnes amb dificultats acadèmiques i mesures a adoptar',
            'Seguiment NESE anterior'
        );

        $summaries = $this->service->inheritedSummaries(Reunion::query()->findOrFail($currentAct));

        $this->assertSame(
            '<p>Acord vigent</p>',
            $summaries["Revisió d'acords adoptats a la sessió anterior"]
        );
        $this->assertSame(
            'Seguiment NESE anterior',
            $summaries['Alumnes amb dificultats acadèmiques i mesures a adoptar']
        );
    }

    public function test_recupera_acta_llegada_sense_id_grupo_del_tutor_del_grup(): void
    {
        $legacyAct = $this->insertAct(10, '2026-09-10 10:00:00', true, null, 'P1');
        $currentAct = $this->insertAct(11, '2026-09-20 10:00:00', false);
        $this->insertOrder($legacyAct, 'Acords adoptats', 'Acord llegat');

        $summaries = $this->service->inheritedSummaries(Reunion::query()->findOrFail($currentAct));

        $this->assertSame(
            'Acord llegat',
            $summaries["Revisió d'acords adoptats a la sessió anterior"]
        );
    }

    public function test_generador_hereta_contingut_i_ompli_els_altres_punts(): void
    {
        $previousAct = $this->insertAct(20, '2026-09-10 10:00:00', true);
        $currentAct = $this->insertAct(21, '2026-09-20 10:00:00', false);
        $this->insertOrder($previousAct, 'Acords adoptats', 'Revisar acord 20');
        $this->insertOrder(
            $previousAct,
            'Alumnes amb dificultats acadèmiques i mesures a adoptar',
            'Revisar mesures 20'
        );

        $reunion = Reunion::query()->findOrFail($currentAct);
        (new MeetingOrderGenerateService($reunion, $this->service))->exec();

        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => $currentAct,
            'descripcion' => "Revisió d'acords adoptats a la sessió anterior",
            'resumen' => 'Revisar acord 20',
        ]);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => $currentAct,
            'descripcion' => 'Alumnes amb dificultats acadèmiques i mesures a adoptar',
            'resumen' => 'Revisar mesures 20',
        ]);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => $currentAct,
            'descripcion' => 'Opinió i/o comentaris dels alumnes',
            'resumen' => ReunionContinuityService::DEFAULT_SUMMARY,
        ]);
    }

    public function test_generador_usa_no_procedeix_quan_no_hi_ha_acta_anterior(): void
    {
        $currentAct = $this->insertAct(30, '2026-09-20 10:00:00', false);

        (new MeetingOrderGenerateService(
            Reunion::query()->findOrFail($currentAct),
            $this->service
        ))->exec();

        $this->assertSame(6, DB::table('ordenes_reuniones')->where('idReunion', $currentAct)->count());
        $this->assertSame(
            0,
            DB::table('ordenes_reuniones')
                ->where('idReunion', $currentAct)
                ->where('resumen', '!=', ReunionContinuityService::DEFAULT_SUMMARY)
                ->count()
        );
    }

    public function test_normalitza_text_null_espais_i_html_buit_abans_arxivar(): void
    {
        $act = $this->insertAct(40, '2026-09-20 10:00:00', false);
        $this->insertOrder($act, 'Null', null, 1);
        $this->insertOrder($act, 'Espais', " \n\t ", 2);
        $this->insertOrder($act, 'HTML', '<p><br>&nbsp;</p>', 3);
        $this->insertOrder($act, 'Contingut', '<p>Mesura acordada</p>', 4);

        $this->service->normaliseEmptySummaries(Reunion::query()->findOrFail($act));

        $this->assertSame(3, DB::table('ordenes_reuniones')
            ->where('idReunion', $act)
            ->where('resumen', ReunionContinuityService::DEFAULT_SUMMARY)
            ->count());
        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => $act,
            'descripcion' => 'Contingut',
            'resumen' => '<p>Mesura acordada</p>',
        ]);
    }

    private function insertAct(
        int $id,
        string $date,
        bool $archived,
        ?string $group = 'G1',
        string $teacher = 'P1',
        int $type = 2
    ): int {
        DB::table('reuniones')->insert([
            'id' => $id,
            'tipo' => $type,
            'idGrupo' => $group,
            'idProfesor' => $teacher,
            'curso' => '2026-2027',
            'fecha' => $date,
            'archivada' => $archived,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        return $id;
    }

    private function insertOrder(int $act, string $description, ?string $summary, int $order = 1): void
    {
        DB::table('ordenes_reuniones')->insert([
            'idReunion' => $act,
            'orden' => $order,
            'descripcion' => $description,
            'resumen' => $summary,
        ]);
    }
}
