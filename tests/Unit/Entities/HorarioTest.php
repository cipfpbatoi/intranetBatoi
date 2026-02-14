<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Horario;
use Tests\TestCase;

class HorarioTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('horarios');
        $schema->dropIfExists('horas');
        $schema->dropIfExists('modulos');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('ocupaciones');

        $schema->create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('hora_ini')->nullable();
            $table->string('hora_fin')->nullable();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->string('idGrupo')->nullable();
            $table->string('dia_semana');
            $table->unsignedInteger('sesion_orden');
            $table->string('ocupacion')->nullable();
            $table->string('modulo')->nullable();
            $table->timestamps();
        });

        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('turno')->nullable();
            $table->timestamps();
        });

        $schema->create('ocupaciones', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('nom')->nullable();
        });
    }

    public function test_horario_semanal_retorna_array_correcte(): void
    {
        DB::connection('sqlite')->table('modulos')->insert([
            ['codigo' => 'M01', 'cliteral' => 'Modul 1', 'vliteral' => 'Modul 1'],
            ['codigo' => 'M02', 'cliteral' => 'Modul 2', 'vliteral' => 'Modul 2'],
        ]);

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '1',
                'idGrupo' => 'G1',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => '1',
                'idGrupo' => 'G1',
                'dia_semana' => 'M',
                'sesion_orden' => 2,
                'ocupacion' => null,
                'modulo' => 'M02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $result = Horario::HorarioSemanal('1');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('L', $result);
        $this->assertArrayHasKey(1, $result['L']);
        $this->assertArrayHasKey('M', $result);
        $this->assertArrayHasKey(2, $result['M']);
    }

    public function test_horario_grupo_retorna_array_correcte(): void
    {
        DB::connection('sqlite')->table('horas')->insert([
            ['codigo' => 1],
            ['codigo' => 2],
        ]);

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => '1',
                'idGrupo' => '101',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'MATH101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => '1',
                'idGrupo' => '101',
                'dia_semana' => 'L',
                'sesion_orden' => 2,
                'ocupacion' => null,
                'modulo' => 'TU01CF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $result = Horario::HorarioGrupo('101');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('L', $result);
        $this->assertArrayHasKey(1, $result['L']);
        $this->assertEquals('MATH101', $result['L'][1]->modulo);
    }
}
