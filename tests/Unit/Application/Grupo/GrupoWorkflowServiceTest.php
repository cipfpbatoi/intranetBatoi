<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Grupo;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Grupo\GrupoWorkflowService;
use Tests\TestCase;

class GrupoWorkflowServiceTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('codigo')->nullable();
            $table->string('ciclo')->nullable();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('ciclo')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->timestamps();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->timestamps();
        });
    }

    public function test_assign_missing_ciclo_actualitza_grups_sense_cicle(): void
    {
        DB::table('ciclos')->insert([
            ['id' => 1, 'codigo' => 'SMXR', 'ciclo' => 'Cicle 1'],
            ['id' => 2, 'codigo' => 'ADMN', 'ciclo' => 'Cicle 2'],
        ]);

        DB::table('grupos')->insert([
            [
                'codigo' => '1SMXR',
                'nombre' => 'Grup A',
                'ciclo' => '',
                'idCiclo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => '2ZZZZ',
                'nombre' => 'Grup B',
                'ciclo' => '',
                'idCiclo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => '1ADMN',
                'nombre' => 'Grup C',
                'ciclo' => 'OK',
                'idCiclo' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $service = new GrupoWorkflowService();
        $updated = $service->assignMissingCiclo();

        $this->assertSame(1, $updated);
        $this->assertSame(1, (int) DB::table('grupos')->where('codigo', '1SMXR')->value('idCiclo'));
        $this->assertNull(DB::table('grupos')->where('codigo', '2ZZZZ')->value('idCiclo'));
        $this->assertSame(2, (int) DB::table('grupos')->where('codigo', '1ADMN')->value('idCiclo'));
    }

    public function test_selected_students_plain_text_retorna_llistat_ordenat(): void
    {
        DB::table('alumnos')->insert([
            [
                'nia' => 'NIA1',
                'nombre' => 'Zoe',
                'apellido1' => 'Zulu',
                'apellido2' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nia' => 'NIA2',
                'nombre' => 'Anna',
                'apellido1' => 'Alba',
                'apellido2' => 'B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nia' => 'NIA3',
                'nombre' => 'Pau',
                'apellido1' => 'Pons',
                'apellido2' => 'C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $payload = [
            'NIA1' => 'on',
            'NIA2' => 'off',
            'NIA3' => 'on',
        ];

        $service = new GrupoWorkflowService();
        $result = $service->selectedStudentsPlainText($payload);

        $this->assertSame('Pons C, Pau; Zulu A, Zoe', $result);
    }
}
