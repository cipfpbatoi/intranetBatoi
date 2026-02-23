<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\MaterialBaja;
use Tests\TestCase;

class MaterialBajaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('materiales_baja');
        $schema->dropIfExists('materiales');
        $schema->dropIfExists('profesores');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->string('api_token', 80)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $schema->create('materiales', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('descripcion', 255)->nullable();
            $table->string('espacio', 10)->nullable();
            $table->date('fechabaja')->nullable();
        });

        $schema->create('materiales_baja', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idMaterial');
            $table->string('idProfesor', 10)->nullable();
            $table->string('motivo', 255)->nullable();
            $table->unsignedTinyInteger('estado')->default(0);
            $table->unsignedTinyInteger('tipo')->default(0);
            $table->string('nuevoEstado', 50)->nullable();
            $table->timestamps();
        });
    }

    public function test_accessors_no_fallen_quan_material_no_existeix(): void
    {
        DB::table('materiales_baja')->insert([
            'id' => 1,
            'idMaterial' => 9999,
            'idProfesor' => null,
            'motivo' => 'test',
            'estado' => 0,
            'tipo' => 0,
            'created_at' => null,
            'updated_at' => null,
        ]);

        $registro = MaterialBaja::findOrFail(1);

        $this->assertSame('', $registro->descripcion);
        $this->assertSame('', $registro->espacio);
        $this->assertSame('No date', $registro->fechaBaja);
    }

    public function test_fecha_baja_prioritza_data_de_material_si_es_mes_antiga(): void
    {
        DB::table('materiales')->insert([
            'id' => 10,
            'descripcion' => 'PC',
            'espacio' => 'AULA1',
            'fechabaja' => '2025-01-01',
        ]);

        DB::table('materiales_baja')->insert([
            'id' => 2,
            'idMaterial' => 10,
            'idProfesor' => null,
            'motivo' => 'test',
            'estado' => 1,
            'tipo' => 0,
            'created_at' => '2025-02-01 10:00:00',
            'updated_at' => '2025-02-01 10:00:00',
        ]);

        $registro = MaterialBaja::findOrFail(2);

        $this->assertSame('2025-01-01', $registro->fechaBaja);
    }
}
