<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\AlumnoFct\AlumnoFctAvalService;
use Intranet\Application\Reunion\ReunionFeValuationService;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Reunion;
use Mockery;
use Tests\TestCase;

/**
 * Proves del servei que crea el punt de valoració FE en actes LFP.
 */
class ReunionFeValuationServiceTest extends TestCase
{
    /**
     * Prepara una base SQLite mínima per a ordres de reunió.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('alumno_fcts');
        $schema->dropIfExists('alumno_resultados');
        $schema->dropIfExists('modulo_grupos');
        $schema->dropIfExists('modulo_ciclos');
        $schema->dropIfExists('modulos');
        $schema->dropIfExists('alumnos_grupos');
        $schema->dropIfExists('alumnos');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('profesores');
        $schema->dropIfExists('fcts');
        $schema->dropIfExists('colaboraciones');
        $schema->dropIfExists('ciclos');
        $schema->dropIfExists('ordenes_reuniones');
        $schema->create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->unsignedInteger('orden');
            $table->string('descripcion')->nullable();
            $table->text('resumen')->nullable();
        });
        $schema->create('colaboraciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idCiclo');
        });
        $schema->create('ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('normativa')->default('LOE');
        });
        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idColaboracion')->nullable();
            $table->unsignedTinyInteger('asociacion')->default(1);
        });
        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('tutor')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
        });
        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('sustituye_a')->nullable();
        });
        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('nombre');
            $table->string('apellido1');
            $table->string('apellido2');
        });
        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });
        $schema->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idFct');
            $table->string('idAlumno');
            $table->string('idProfesor')->nullable();
            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->unsignedTinyInteger('correoAlumno')->default(0);
            $table->unsignedInteger('horas')->default(0);
        });
        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('cliteral');
            $table->string('vliteral');
        });
        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo');
            $table->unsignedInteger('idCiclo');
            $table->string('curso')->default('2');
        });
        $schema->create('modulo_grupos', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloCiclo');
            $table->string('idGrupo');
        });
        $schema->create('alumno_resultados', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumno');
            $table->unsignedInteger('idModuloGrupo');
            $table->unsignedTinyInteger('nota')->default(0);
            $table->string('observaciones')->nullable();
        });
    }

    /**
     * Verifica que l'acta final LFP rep el punt FE editable.
     */
    public function test_crea_punt_fe_en_acta_final_lfp(): void
    {
        DB::table('ordenes_reuniones')->insert([
            ['idReunion' => 10, 'orden' => 1, 'descripcion' => 'Observacions', 'resumen' => 'Text'],
        ]);

        $order = $this->serviceWithAvalFcts(collect())->ensureOrder(
            $this->makeReunion(10, 7, 34),
            'LFP'
        );

        $this->assertNotNull($order);
        $this->assertSame(2, (int) $order->orden);
        $this->assertSame(ReunionFeValuationService::ORDER_DESCRIPTION, $order->descripcion);
        $this->assertStringContainsString('Alumnat apte', (string) $order->resumen);
        $this->assertStringContainsString('Alumnat no apte', (string) $order->resumen);
        $this->assertStringContainsString('notes reals dels mòduls', (string) $order->resumen);
        $this->assertStringContainsString('convocatòria extraordinària', (string) $order->resumen);
    }

    /**
     * Verifica que el servei conserva el punt FE ja editat pel tutor.
     */
    public function test_no_duplica_punt_fe_si_ja_existix(): void
    {
        DB::table('ordenes_reuniones')->insert([
            [
                'idReunion' => 10,
                'orden' => 9,
                'descripcion' => ReunionFeValuationService::ORDER_DESCRIPTION,
                'resumen' => 'Resum del tutor',
            ],
        ]);

        $order = $this->serviceWithAvalFcts(collect())->ensureOrder(
            $this->makeReunion(10, 7, 35),
            'LFP'
        );

        $this->assertSame('Resum del tutor', $order?->resumen);
        $this->assertSame(1, DB::table('ordenes_reuniones')->count());
    }

    /**
     * Verifica que una plantilla existent no es matxaca en entrar a l'acta.
     */
    public function test_no_matxaca_punt_fe_si_ja_existix(): void
    {
        $this->seedKnownFctData();
        $summary = $this->serviceWithAvalFcts(collect())->defaultSummary();
        DB::table('ordenes_reuniones')->insert([
            [
                'idReunion' => 10,
                'orden' => 9,
                'descripcion' => ReunionFeValuationService::ORDER_DESCRIPTION,
                'resumen' => $summary,
            ],
        ]);

        $order = $this->serviceWithAvalFcts($this->avalFcts())->ensureOrder(
            $this->makeReunion(10, 7, 34),
            'LFP'
        );

        $this->assertSame($summary, (string) $order?->resumen);
        $this->assertStringNotContainsString('Apta Test, Anna - Apte - 120 hores', (string) $order?->resumen);
        $this->assertSame(1, DB::table('ordenes_reuniones')->count());
    }

    /**
     * Verifica que el resum usa les FCT del tutor si no es pot resoldre el grup.
     */
    public function test_resum_fe_preompli_dades_fct_conegudes_del_tutor(): void
    {
        $this->seedKnownFctData();

        $summary = $this->serviceWithAvalFcts($this->avalFcts())->defaultSummaryForReunion(
            $this->makeReunion(10, 7, 34)
        );

        $this->assertStringNotContainsString('Dades conegudes pel sistema', $summary);
        $this->assertStringContainsString('Apta Test, Anna - Apte - 120 hores', $summary);
        $this->assertStringContainsString('Noapta Test, Noa - No Apte - 80 hores', $summary);
        $this->assertStringContainsString('Exempta Test, Eva - Convalidat/Exempt', $summary);
        $this->assertStringContainsString('Renuncia Test, Rita - Renúncia', $summary);
        $this->assertStringContainsString('Loe Test, Laia - Apte - 200 hores', $summary);
    }

    /**
     * Verifica que es crea el punt 10 amb notes reals introduïdes.
     */
    public function test_crea_punt_notes_reals_per_alumnat_no_apte_o_renuncia(): void
    {
        $this->seedKnownFctData();
        $this->seedModuleGrades();

        $this->serviceWithAvalFcts($this->avalFcts())->ensureOrder(
            $this->makeReunion(10, 7, 34),
            'LFP'
        );

        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => 10,
            'descripcion' => ReunionFeValuationService::NOTES_ORDER_DESCRIPTION,
        ]);

        $summary = (string) DB::table('ordenes_reuniones')
            ->where('idReunion', 10)
            ->where('descripcion', ReunionFeValuationService::NOTES_ORDER_DESCRIPTION)
            ->value('resumen');

        $this->assertStringContainsString('Noapta Test, Noa', $summary);
        $this->assertStringContainsString('Renuncia Test, Rita', $summary);
        $this->assertStringContainsString('Mòdul pràctic', $summary);
        $this->assertStringContainsString('<strong>Noapta Test, Noa</strong><ul><li><strong>Mòdul pràctic</strong>: 6</li></ul>', $summary);
        $this->assertStringNotContainsString('Apta Test, Anna', $summary);
    }

    /**
     * Verifica que l'acta ordinària prepara els mòduls de l'alumnat no apte o amb renúncia.
     */
    public function test_prepara_dades_per_a_notes_reals_en_acta_ordinaria(): void
    {
        $this->seedKnownFctData();
        $this->seedModuleGrades();

        $data = $this->serviceWithAvalFcts($this->avalFcts())->gradeInputData(
            $this->makeReunion(10, 7, 34)
        );

        $this->assertSame(['A2', 'A4'], $data['fcts']->pluck('idAlumno')->all());
        $this->assertCount(1, $data['modulesByStudent']->get('A2'));
        $this->assertTrue($data['results']->has('A2-1'));
        $this->assertFalse($data['results']->has('A1-1'));
        $this->assertSame(
            [0 => 'No Avaluat', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10'],
            $data['gradeOptions']
        );
    }

    /**
     * Verifica que es guarden les notes FE i es refresque el punt de notes reals.
     */
    public function test_guarda_notes_reals_de_moduls_des_de_lacta(): void
    {
        $this->seedKnownFctData();
        $this->seedModuleGrades();

        DB::table('ordenes_reuniones')->insert([
            [
                'idReunion' => 10,
                'orden' => 1,
                'descripcion' => ReunionFeValuationService::NOTES_ORDER_DESCRIPTION,
                'resumen' => 'Text anterior',
            ],
        ]);

        $saved = $this->serviceWithAvalFcts($this->avalFcts())->saveModuleGrades(
            $this->makeReunion(10, 7, 34),
            [
                'A2' => [
                    1 => ['nota' => 7, 'observaciones' => 'Nota real revisada'],
                ],
                'A4' => [
                    1 => ['nota' => 4, 'observaciones' => 'Nota fora de rang'],
                ],
                'A1' => [
                    1 => ['nota' => 9, 'observaciones' => 'No és alumnat afectat'],
                ],
            ]
        );

        $this->assertSame(1, $saved);
        $this->assertDatabaseHas('alumno_resultados', [
            'idAlumno' => 'A2',
            'idModuloGrupo' => 1,
            'nota' => 7,
            'observaciones' => 'Nota real revisada',
        ]);
        $this->assertDatabaseMissing('alumno_resultados', [
            'idAlumno' => 'A4',
            'idModuloGrupo' => 1,
            'nota' => 4,
        ]);
        $this->assertDatabaseMissing('alumno_resultados', [
            'idAlumno' => 'A1',
            'idModuloGrupo' => 1,
            'nota' => 9,
        ]);

        $summary = (string) DB::table('ordenes_reuniones')
            ->where('idReunion', 10)
            ->where('descripcion', ReunionFeValuationService::NOTES_ORDER_DESCRIPTION)
            ->value('resumen');

        $this->assertStringContainsString('Noapta Test, Noa', $summary);
        $this->assertStringContainsString('7', $summary);
        $this->assertStringContainsString('<strong>Noapta Test, Noa</strong><ul><li><strong>Mòdul pràctic</strong>: 7</li></ul>', $summary);
        $this->assertStringNotContainsString('Text anterior', $summary);
    }

    /**
     * Verifica que No Avaluat és vàlid però no es mostra en el punt de notes de l'acta.
     */
    public function test_no_avaluat_no_ix_en_resum_de_lacta(): void
    {
        $this->seedKnownFctData();
        $this->seedModuleGrades();
        DB::table('alumno_resultados')->where('idAlumno', 'A2')->update(['nota' => 0]);
        DB::table('alumno_resultados')->where('idAlumno', 'A4')->update(['nota' => 5]);

        $summary = $this->serviceWithAvalFcts($this->avalFcts())->notesSummaryForTutor('P1');

        $this->assertStringNotContainsString('Noapta Test, Noa', $summary);
        $this->assertStringContainsString('Renuncia Test, Rita', $summary);
    }

    /**
     * Verifica que No Avaluat compta com a resultat emplenat per a no bloquejar l'acta.
     */
    public function test_no_avaluat_no_compta_com_a_pendent(): void
    {
        $this->seedKnownFctData();
        $this->seedModuleGrades();
        DB::table('alumno_resultados')->where('idAlumno', 'A2')->update(['nota' => 0]);
        DB::table('alumno_resultados')->where('idAlumno', 'A4')->update(['nota' => 5]);

        $faltants = $this->serviceWithAvalFcts($this->avalFcts())->missingModuleGrades(
            $this->makeReunion(10, 7, 34)
        );

        $this->assertSame([], $faltants);
    }

    /**
     * Verifica que un punt antic de notes s'elimina si només queda No Avaluat.
     */
    public function test_elimina_punt_notes_antic(): void
    {
        $this->seedKnownFctData();
        $this->seedModuleGrades();
        DB::table('alumno_resultados')->whereIn('idAlumno', ['A2', 'A4'])->update(['nota' => 0]);
        DB::table('ordenes_reuniones')->insert([
            [
                'idReunion' => 10,
                'orden' => 1,
                'descripcion' => ReunionFeValuationService::NOTES_ORDER_DESCRIPTION,
                'resumen' => 'Text anterior',
            ],
        ]);

        $this->serviceWithAvalFcts($this->avalFcts())->refreshNotesOrder(
            $this->makeReunion(10, 7, 34)
        );

        $this->assertDatabaseMissing('ordenes_reuniones', [
            'idReunion' => 10,
            'descripcion' => ReunionFeValuationService::NOTES_ORDER_DESCRIPTION,
        ]);
    }

    /**
     * Verifica que es detecten notes pendents tant en no aptes com en renúncies.
     */
    public function test_detecta_notes_pendents_de_no_aptes_i_renuncies(): void
    {
        $this->seedKnownFctData();
        $this->seedModuleGrades();
        DB::table('alumno_resultados')->where('idAlumno', 'A2')->delete();
        DB::table('alumno_resultados')->where('idAlumno', 'A4')->delete();

        $faltants = $this->serviceWithAvalFcts($this->avalFcts())->missingModuleGrades(
            $this->makeReunion(10, 7, 34)
        );

        $this->assertCount(2, $faltants);
        $this->assertSame('Noapta Test, Noa', $faltants[0]['alumno']);
        $this->assertSame('Renuncia Test, Rita', $faltants[1]['alumno']);
    }

    /**
     * Verifica que no s'afegix el punt FE fora d'actes finals o extraordinàries LFP.
     */
    public function test_no_crea_punt_fe_en_actes_no_lfp_o_no_finals(): void
    {
        $service = new ReunionFeValuationService();

        $this->assertNull($service->ensureOrder($this->makeReunion(10, 7, 34), 'LOE'));
        $this->assertNull($service->ensureOrder($this->makeReunion(11, 7, 31), 'LFP'));

        $this->assertSame(0, DB::table('ordenes_reuniones')->count());
    }

    /**
     * Crea una reunió Eloquent mínima per a les proves.
     */
    private function makeReunion(int $id, int $tipo, int $numero): Reunion
    {
        $reunion = new Reunion([
            'tipo' => $tipo,
            'numero' => $numero,
            'curso' => '2025',
            'fecha' => '2026-06-04 10:00:00',
            'descripcion' => 'Acta',
            'idProfesor' => 'P1',
            'idEspacio' => 'A1',
        ]);
        $reunion->id = $id;
        $reunion->exists = true;

        return $reunion;
    }

    /**
     * Crea dades conegudes de FCT per al grup LFP de prova.
     */
    private function seedKnownFctData(): void
    {
        DB::table('ciclos')->insert([
            ['id' => 100, 'normativa' => 'LOE'],
            ['id' => 200, 'normativa' => 'LOE'],
        ]);
        DB::table('profesores')->insert([
            ['dni' => 'P1', 'sustituye_a' => null],
        ]);
        DB::table('colaboraciones')->insert([
            ['id' => 1, 'idCiclo' => 100],
            ['id' => 2, 'idCiclo' => 100],
            ['id' => 3, 'idCiclo' => 200],
        ]);
        DB::table('fcts')->insert([
            ['id' => 1, 'idColaboracion' => 1, 'asociacion' => 1],
            ['id' => 2, 'idColaboracion' => 1, 'asociacion' => 1],
            ['id' => 3, 'idColaboracion' => 2, 'asociacion' => 2],
            ['id' => 4, 'idColaboracion' => 2, 'asociacion' => 1],
            ['id' => 5, 'idColaboracion' => 3, 'asociacion' => 1],
        ]);
        DB::table('grupos')->insert([
            ['codigo' => '2LFP', 'nombre' => 'Segon LFP', 'tutor' => 'P1', 'idCiclo' => 100],
            ['codigo' => '2LOE', 'nombre' => 'Segon LOE', 'tutor' => 'P1', 'idCiclo' => 200],
        ]);
        DB::table('alumnos')->insert([
            ['nia' => 'A1', 'nombre' => 'Anna', 'apellido1' => 'Apta', 'apellido2' => 'Test'],
            ['nia' => 'A2', 'nombre' => 'Noa', 'apellido1' => 'Noapta', 'apellido2' => 'Test'],
            ['nia' => 'A3', 'nombre' => 'Eva', 'apellido1' => 'Exempta', 'apellido2' => 'Test'],
            ['nia' => 'A4', 'nombre' => 'Rita', 'apellido1' => 'Renuncia', 'apellido2' => 'Test'],
            ['nia' => 'A5', 'nombre' => 'Laia', 'apellido1' => 'Loe', 'apellido2' => 'Test'],
        ]);
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A1', 'idGrupo' => '2LFP'],
            ['idAlumno' => 'A2', 'idGrupo' => '2LFP'],
            ['idAlumno' => 'A3', 'idGrupo' => '2LFP'],
            ['idAlumno' => 'A4', 'idGrupo' => '2LFP'],
            ['idAlumno' => 'A5', 'idGrupo' => '2LOE'],
        ]);
        DB::table('alumno_fcts')->insert([
            ['idFct' => 1, 'idAlumno' => 'A1', 'idProfesor' => 'P1', 'calificacion' => 1, 'horas' => 120],
            ['idFct' => 2, 'idAlumno' => 'A2', 'idProfesor' => 'P1', 'calificacion' => 0, 'horas' => 80],
            ['idFct' => 3, 'idAlumno' => 'A3', 'idProfesor' => 'P1', 'calificacion' => 2, 'horas' => 0],
            ['idFct' => 4, 'idAlumno' => 'A4', 'idProfesor' => 'P1', 'calificacion' => 3, 'horas' => 0],
            ['idFct' => 5, 'idAlumno' => 'A5', 'idProfesor' => 'P1', 'calificacion' => 1, 'horas' => 200],
        ]);
    }

    /**
     * Crea notes reals de mòduls ja introduïdes.
     */
    private function seedModuleGrades(): void
    {
        DB::table('modulos')->insert([
            ['codigo' => 'M1', 'cliteral' => 'Módulo práctico', 'vliteral' => 'Mòdul pràctic'],
        ]);
        DB::table('modulo_ciclos')->insert([
            ['id' => 1, 'idModulo' => 'M1', 'idCiclo' => 100, 'curso' => '2'],
        ]);
        DB::table('modulo_grupos')->insert([
            ['id' => 1, 'idModuloCiclo' => 1, 'idGrupo' => '2LFP'],
        ]);
        DB::table('alumno_resultados')->insert([
            ['idAlumno' => 'A1', 'idModuloGrupo' => 1, 'nota' => 8],
            ['idAlumno' => 'A2', 'idModuloGrupo' => 1, 'nota' => 6],
            ['idAlumno' => 'A4', 'idModuloGrupo' => 1, 'nota' => 5],
        ]);
    }

    /**
     * Retorna les mateixes FCT que simula el servei del panell `/avalFct`.
     *
     * @return \Illuminate\Support\Collection<int, AlumnoFct>
     */
    private function avalFcts(): \Illuminate\Support\Collection
    {
        return AlumnoFct::query()
            ->with('Alumno')
            ->whereIn('idAlumno', ['A1', 'A2', 'A3', 'A4', 'A5'])
            ->get();
    }

    /**
     * Crea el servei FE amb el mateix origen de dades que `/avalFct`.
     *
     * @param \Illuminate\Support\Collection<int, AlumnoFct> $fcts
     * @return ReunionFeValuationService
     */
    private function serviceWithAvalFcts(\Illuminate\Support\Collection $fcts): ReunionFeValuationService
    {
        $avalService = Mockery::mock(AlumnoFctAvalService::class);
        $avalService->shouldReceive('latestByProfesor')
            ->with('P1')
            ->andReturn($fcts);

        return new ReunionFeValuationService($avalService);
    }

}
