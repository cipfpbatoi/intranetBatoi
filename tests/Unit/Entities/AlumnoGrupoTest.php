<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\AlumnoGrupo;
use Tests\TestCase;

class AlumnoGrupoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    public function test_find_amb_array_resol_clau_composta(): void
    {
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A001', 'idGrupo' => 'G1', 'subGrupo' => 'A', 'posicion' => '1'],
            ['idAlumno' => 'A001', 'idGrupo' => 'G2', 'subGrupo' => 'B', 'posicion' => '2'],
        ]);

        $row = AlumnoGrupo::find(['A001', 'G2']);

        $this->assertNotNull($row);
        $this->assertSame('G2', $row->idGrupo);
    }

    public function test_find_estandard_funciona_per_primary_key(): void
    {
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A002', 'idGrupo' => 'G9', 'subGrupo' => 'A', 'posicion' => '3'],
        ]);

        $row = AlumnoGrupo::find('A002');

        $this->assertNotNull($row);
        $this->assertSame('A002', $row->idAlumno);
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('alumnos_grupos');

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
            $table->string('subGrupo')->nullable();
            $table->string('posicion')->nullable();
        });
    }
}
