<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Resultado;
use Tests\TestCase;

class ResultadoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('resultados');
        $schema->dropIfExists('modulo_grupos');
        $schema->dropIfExists('profesores');

        $schema->create('modulo_grupos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idGrupo', 10)->nullable();
            $table->unsignedInteger('idModuloCiclo')->nullable();
        });

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('password')->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
            $table->timestamps();
        });

        $schema->create('resultados', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloGrupo')->nullable();
            $table->unsignedInteger('evaluacion')->nullable();
            $table->string('idProfesor', 10)->nullable();
            $table->timestamps();
        });
    }

    public function test_accessors_son_null_safe_quan_falten_relacions(): void
    {
        DB::table('resultados')->insert([
            'id' => 1,
            'idModuloGrupo' => 999,
            'evaluacion' => 1,
            'idProfesor' => 'PX',
        ]);

        $resultado = Resultado::query()->findOrFail(1);

        $this->assertSame('', $resultado->Modulo);
        $this->assertSame('', $resultado->XProfesor);
    }

    public function test_xprofesor_torna_shortname_quan_existeix_profesor(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P1',
            'nombre' => 'ANNA',
            'apellido1' => 'SERRA',
            'apellido2' => 'MATEU',
            'password' => 'x',
            'activo' => 1,
        ]);

        DB::table('resultados')->insert([
            'id' => 2,
            'idModuloGrupo' => null,
            'evaluacion' => 2,
            'idProfesor' => 'P1',
        ]);

        $resultado = Resultado::query()->findOrFail(2);

        $this->assertSame('Anna Serra', $resultado->XProfesor);
    }
}

