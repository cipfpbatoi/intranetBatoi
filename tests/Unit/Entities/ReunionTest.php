<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use Tests\TestCase;

/**
 * Proves unitàries dels accessors i consultes de l'entitat Reunió.
 */
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
        $schema->dropIfExists('espacios');

        $schema->create('departamentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('literal')->nullable();
            $table->string('cliteral')->nullable();
        });

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('departamento')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->string('sustituye_a', 10)->nullable();
        });

        $schema->create('espacios', function (Blueprint $table): void {
            $table->string('aula', 10)->primary();
            $table->string('descripcion')->nullable();
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
            $table->string('idEspacio', 10)->nullable();
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

    public function test_lloc_reunio_usa_descripcio_i_cau_al_codi_si_esta_buida(): void
    {
        DB::table('espacios')->insert([
            ['aula' => 'SALA1', 'descripcion' => 'Sala de reunions'],
            ['aula' => 'A101', 'descripcion' => null],
        ]);

        $described = Reunion::query()->create([
            'idProfesor' => 'P100',
            'idEspacio' => 'SALA1',
            'tipo' => 2,
        ]);
        $withoutDescription = Reunion::query()->create([
            'idProfesor' => 'P100',
            'idEspacio' => 'A101',
            'tipo' => 2,
        ]);

        $this->assertSame('Sala de reunions', $described->lloc_reunio);
        $this->assertSame('A101', $withoutDescription->lloc_reunio);
    }

    public function test_selector_de_grup_mostra_nomes_les_tutories_del_professor(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P1', 'rol' => 3],
            ['dni' => 'P2', 'rol' => 3],
        ]);
        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup propi', 'tutor' => 'P1'],
            ['codigo' => 'G2', 'nombre' => 'Grup alié', 'tutor' => 'P2'],
        ]);
        $this->actingAs(Profesor::query()->findOrFail('P1'), 'profesor');

        $options = (new Reunion())->getIdGrupoOptions();

        $this->assertSame(['G1' => 'Grup propi'], $options);
    }

    public function test_selector_conserva_el_grup_actual_d_una_acta_antiga(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P1', 'rol' => 3],
            ['dni' => 'P2', 'rol' => 3],
        ]);
        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup actual', 'tutor' => 'P1'],
            ['codigo' => 'G2', 'nombre' => 'Grup anterior', 'tutor' => 'P2'],
        ]);
        $this->actingAs(Profesor::query()->findOrFail('P1'), 'profesor');

        $options = (new Reunion(['idGrupo' => 'G2']))->getIdGrupoOptions();

        $this->assertSame([
            'G1' => 'Grup actual',
            'G2' => 'Grup anterior',
        ], $options);
    }

    public function test_selector_de_direccio_mostra_tots_els_grups(): void
    {
        DB::table('profesores')->insert(['dni' => 'DIR1', 'rol' => config('roles.rol.direccion')]);
        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup u', 'tutor' => 'P1'],
            ['codigo' => 'G2', 'nombre' => 'Grup dos', 'tutor' => 'P2'],
        ]);
        $this->actingAs(Profesor::query()->findOrFail('DIR1'), 'profesor');

        $options = (new Reunion())->getIdGrupoOptions();

        $this->assertSame([
            'G1' => 'Grup u',
            'G2' => 'Grup dos',
        ], $options);
    }

    public function test_selector_reconeix_el_grup_del_tutor_substituit(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'PBASE', 'rol' => 3, 'sustituye_a' => null],
            ['dni' => 'PSUB', 'rol' => 3, 'sustituye_a' => 'PBASE'],
        ]);
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'nombre' => 'Grup del professor substituït',
            'tutor' => 'PBASE',
        ]);
        $this->actingAs(Profesor::query()->findOrFail('PSUB'), 'profesor');

        $options = (new Reunion())->getIdGrupoOptions();

        $this->assertSame(['G1' => 'Grup del professor substituït'], $options);
    }
}
