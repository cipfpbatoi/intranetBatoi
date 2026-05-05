<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ProfesorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Storage::fake('local');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('faltas_profesores');
        $schema->dropIfExists('horarios');
        $schema->dropIfExists('horas');
        $schema->dropIfExists('miembros');
        $schema->dropIfExists('profesores');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->string('emailItaca')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('movil1')->nullable();
            $table->string('movil2')->nullable();
            $table->string('sexo')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->unsignedInteger('departamento')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('foto')->nullable();
            $table->unsignedInteger('rol')->default(1);
            $table->string('idioma')->nullable();
            $table->unsignedTinyInteger('mostrar')->default(1);
            $table->string('password')->nullable();
            $table->string('api_token', 80)->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
            $table->string('sustituye_a')->nullable();
            $table->timestamps();
        });

        $schema->create('miembros', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idGrupoTrabajo', 20);
            $table->string('idProfesor', 10);
        });

        $schema->create('faltas_profesores', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10)->nullable();
            $table->date('dia')->nullable();
            $table->string('entrada')->nullable();
            $table->string('salida')->nullable();
            $table->timestamps();
        });

        $schema->create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('turno')->nullable();
            $table->string('hora_ini')->nullable();
            $table->string('hora_fin')->nullable();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('modulo')->nullable();
            $table->string('idGrupo')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('aula')->nullable();
            $table->string('dia_semana');
            $table->unsignedInteger('sesion_orden');
            $table->string('plantilla')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_scope_grupo_t_només_torna_professorat_actiu_del_grup(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P1', 'activo' => 1, 'fecha_baja' => null],
            ['dni' => 'P2', 'activo' => 1, 'fecha_baja' => '2026-01-01'],
            ['dni' => 'P3', 'activo' => 0, 'fecha_baja' => null],
        ]);

        DB::table('miembros')->insert([
            ['idGrupoTrabajo' => 'GT1', 'idProfesor' => 'P1'],
            ['idGrupoTrabajo' => 'GT1', 'idProfesor' => 'P2'],
            ['idGrupoTrabajo' => 'GT1', 'idProfesor' => 'P3'],
            ['idGrupoTrabajo' => 'GT2', 'idProfesor' => 'P1'],
        ]);

        $result = Profesor::query()->GrupoT('GT1')->pluck('dni')->all();

        $this->assertSame(['P1'], $result);
    }

    public function test_get_substituts_talla_cicles_i_no_entra_en_bucle(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'A', 'activo' => 1, 'sustituye_a' => 'B'],
            ['dni' => 'B', 'activo' => 1, 'sustituye_a' => 'A'],
        ]);

        $this->assertSame(['A', 'B'], Profesor::getSubstituts('A'));
    }

    public function test_accessors_entrada_eixida_i_filename_son_segurs(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'PX',
            'activo' => 1,
            'foto' => null,
        ]);

        $profesor = Profesor::query()->findOrFail('PX');

        $this->assertSame(' ', $profesor->entrada);
        $this->assertSame(' ', $profesor->salida);
        $this->assertSame('', $profesor->fileName);
    }

    public function test_horario_mostra_primera_franja_temporal_acceptada_i_vigent(): void
    {
        Carbon::setTestNow('2026-05-04 10:00:00');

        DB::table('profesores')->insert([
            'dni' => 'PT',
            'activo' => 1,
            'nombre' => 'Anna',
            'apellido1' => 'Boto',
            'apellido2' => 'Ibor',
        ]);
        DB::table('horas')->insert([
            [
                'codigo' => 1,
                'turno' => 'M',
                'hora_ini' => '08:00',
                'hora_fin' => '09:00',
            ],
            [
                'codigo' => 2,
                'turno' => 'M',
                'hora_ini' => '09:00',
                'hora_fin' => '10:00',
            ],
        ]);

        $horarioId = DB::table('horarios')->insertGetId([
            'idProfesor' => 'PT',
            'dia_semana' => 'L',
            'sesion_orden' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::disk('local')->put('/horarios/PT/proposta.json', json_encode([
            'id' => 'proposta',
            'dni' => 'PT',
            'estado' => 'Aceptado',
            'fecha_inicio' => '2026-05-01',
            'fecha_fin' => '2026-05-08',
            'updated_at' => '2026-05-03 12:00:00',
            'cambios' => [
                [
                    'id' => (string) $horarioId,
                    'de' => '2-L',
                    'a' => '1-L',
                ],
            ],
        ]));

        $profesor = Profesor::query()->findOrFail('PT');

        $this->assertSame('09:00 - 10:00 (temporal: 08:00 - 09:00)', $profesor->horario);
    }
}
