<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Proves de migració de la numeració d'actes existents.
 */
class ReunionActNumberMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('departamento')->nullable();
        });
        Schema::create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('tutor', 10)->nullable();
        });
        Schema::create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo');
            $table->string('grupo')->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->string('curso', 20);
            $table->unsignedTinyInteger('numero')->nullable();
            $table->dateTime('fecha');
            $table->string('idProfesor', 10);
        });
    }

    public function test_numera_les_actes_existents_per_data_i_organ_sense_alterar_numero(): void
    {
        DB::table('profesores')->insert(['dni' => 'P1', 'departamento' => 10]);
        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'tutor' => 'P1'],
            ['codigo' => 'G2', 'tutor' => 'P2'],
        ]);
        DB::table('reuniones')->insert([
            [
                'id' => 1,
                'tipo' => 7,
                'idGrupo' => 'G1',
                'curso' => '2026-2027',
                'numero' => 34,
                'fecha' => '2026-10-02 10:00:00',
                'idProfesor' => 'P1',
            ],
            [
                'id' => 2,
                'tipo' => 2,
                'idGrupo' => 'G1',
                'curso' => '2026-2027',
                'numero' => 1,
                'fecha' => '2026-10-01 10:00:00',
                'idProfesor' => 'P1',
            ],
            [
                'id' => 3,
                'tipo' => 2,
                'idGrupo' => null,
                'curso' => '2026-2027',
                'numero' => 2,
                'fecha' => '2026-10-03 10:00:00',
                'idProfesor' => 'P1',
            ],
            [
                'id' => 4,
                'tipo' => 2,
                'idGrupo' => 'G2',
                'curso' => '2026-2027',
                'numero' => 1,
                'fecha' => '2026-10-01 09:00:00',
                'idProfesor' => 'P2',
            ],
        ]);

        $migration = require database_path(
            'migrations/2026_09_29_120000_add_act_numbering_to_reuniones_table.php'
        );
        $migration->up();

        $this->assertDatabaseHas('reuniones', [
            'id' => 2,
            'organo_acta' => 'grup:G1',
            'numero_acta' => 1,
            'numero' => 1,
        ]);
        $this->assertDatabaseHas('reuniones', [
            'id' => 1,
            'organo_acta' => 'grup:G1',
            'numero_acta' => 2,
            'numero' => 34,
        ]);
        $this->assertDatabaseHas('reuniones', [
            'id' => 3,
            'organo_acta' => 'grup:G1',
            'numero_acta' => 3,
        ]);
        $this->assertDatabaseHas('reuniones', [
            'id' => 4,
            'organo_acta' => 'grup:G2',
            'numero_acta' => 1,
        ]);
        $this->assertDatabaseHas('reunion_acta_counters', [
            'curso' => '2026-2027',
            'organo' => 'grup:G1',
            'ultimo_numero' => 3,
        ]);
    }
}
