<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Reunion;
use Tests\TestCase;

class ReunionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('reuniones');
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

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo')->nullable();
            $table->unsignedTinyInteger('numero')->nullable();
            $table->string('idProfesor', 10)->nullable();
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
}
