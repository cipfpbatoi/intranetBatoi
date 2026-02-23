<?php

namespace Tests\Unit\Services;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Reunion;
use Intranet\Services\School\ReunionService;
use Tests\TestCase;

class ReunionServiceTest extends TestCase
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
        $schema->dropIfExists('asistencias');
        $schema->dropIfExists('alumno_reuniones');
        $schema->dropIfExists('reuniones');
        $schema->dropIfExists('profesores');
        $schema->dropIfExists('alumnos');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 12)->primary();
        });

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10)->nullable();
            $table->timestamps();
        });

        $schema->create('asistencias', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->string('idProfesor', 10);
            $table->unsignedTinyInteger('asiste')->default(0);
        });

        $schema->create('alumno_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->string('idAlumno', 12);
            $table->unsignedTinyInteger('capacitats')->default(0);
        });
    }

    public function test_add_i_remove_profesor(): void
    {
        DB::table('profesores')->insert(['dni' => 'P100']);
        DB::table('reuniones')->insert([
            'id' => 1,
            'idProfesor' => 'P100',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $reunion = Reunion::query()->findOrFail(1);
        $service = new ReunionService();

        $service->addProfesor($reunion, 'P100');
        $this->assertSame(1, DB::table('asistencias')
            ->where('idReunion', 1)
            ->where('idProfesor', 'P100')
            ->where('asiste', 1)
            ->count());

        $service->removeProfesor($reunion, 'P100');
        $this->assertSame(0, DB::table('asistencias')
            ->where('idReunion', 1)
            ->where('idProfesor', 'P100')
            ->count());
    }

    public function test_add_i_remove_alumno(): void
    {
        DB::table('profesores')->insert(['dni' => 'P200']);
        DB::table('alumnos')->insert(['nia' => 'A100']);
        DB::table('reuniones')->insert([
            'id' => 2,
            'idProfesor' => 'P200',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $reunion = Reunion::query()->findOrFail(2);
        $service = new ReunionService();

        $service->addAlumno($reunion, 'A100', 3);
        $this->assertSame(1, DB::table('alumno_reuniones')
            ->where('idReunion', 2)
            ->where('idAlumno', 'A100')
            ->where('capacitats', 3)
            ->count());

        $service->removeAlumno($reunion, 'A100');
        $this->assertSame(0, DB::table('alumno_reuniones')
            ->where('idReunion', 2)
            ->where('idAlumno', 'A100')
            ->count());
    }
}

