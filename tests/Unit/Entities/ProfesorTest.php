<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('faltas_profesores');
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
}
