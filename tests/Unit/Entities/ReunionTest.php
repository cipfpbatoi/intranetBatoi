<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Grupo;
use Intranet\Entities\Reunion;
use Tests\TestCase;

class ReunionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('reuniones');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('profesores');
        $schema->dropIfExists('departamentos');

        $schema->create('departamentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('literal')->nullable();
            $table->string('cliteral')->nullable();
        });

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('departamento')->nullable();
            $table->string('sustituye_a', 10)->nullable();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('tutor', 10)->nullable();
            $table->unsignedTinyInteger('curso')->nullable();
            $table->string('turno')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->timestamps();
        });

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo')->nullable();
            $table->unsignedTinyInteger('numero')->nullable();
            $table->string('idProfesor', 10)->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->timestamps();
        });
    }

    public function test_departamento_accessor_es_null_safe(): void
    {
        Reunion::query()->create([
            'id' => 1,
            'idProfesor' => 'NOPE',
            'tipo' => 10,
            'numero' => 21,
        ]);

        $reunion = Reunion::query()->findOrFail(1);

        $this->assertSame('', $reunion->departamento);
    }

    public function test_scope_convocante_no_falla_si_no_existe_profesor(): void
    {
        $count = Reunion::query()->convocante('NOPE')->count();

        $this->assertSame(0, $count);
    }

    public function test_mostra_notes_fe_retorna_true_per_avaluacio_final_de_primer_semipresencial(): void
    {
        $reunion = Reunion::query()->create([
            'idProfesor' => 'P100',
            'tipo' => 7,
            'numero' => 34,
        ]);

        $grupo = new Grupo();
        $grupo->curso = 1;
        $grupo->turno = 'S';

        $grupoService = $this->createMock(GrupoService::class);
        $grupoService->method('largestByTutor')->with('P100')->willReturn($grupo);
        app()->instance(GrupoService::class, $grupoService);

        $this->assertTrue($reunion->mostra_notes_fe);
    }

    public function test_mostra_notes_fe_retorna_false_per_avaluacio_final_de_primer_no_semipresencial(): void
    {
        $reunion = Reunion::query()->create([
            'idProfesor' => 'P200',
            'tipo' => 7,
            'numero' => 34,
        ]);

        $grupo = new Grupo();
        $grupo->curso = 1;
        $grupo->turno = 'M';

        $grupoService = $this->createMock(GrupoService::class);
        $grupoService->method('largestByTutor')->with('P200')->willReturn($grupo);
        app()->instance(GrupoService::class, $grupoService);

        $this->assertFalse($reunion->mostra_notes_fe);
    }

    public function test_grupo_clase_preferix_id_grupo_abans_del_tutor_convocant(): void
    {
        Grupo::query()->create([
            'codigo' => 'G1',
            'nombre' => 'Primer LFP',
            'tutor' => 'P900',
            'curso' => 1,
            'turno' => 'S',
        ]);

        $reunion = Reunion::query()->create([
            'idProfesor' => 'P100',
            'idGrupo' => 'G1',
            'tipo' => 7,
            'numero' => 34,
        ]);

        $this->assertSame('G1', $reunion->grupoClase?->codigo);
        $this->assertSame('Primer LFP', $reunion->xgrupo);
        $this->assertTrue($reunion->mostra_notes_fe);
    }
}
