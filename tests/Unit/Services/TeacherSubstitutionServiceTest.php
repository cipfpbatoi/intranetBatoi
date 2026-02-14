<?php

namespace Tests\Unit\Services;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Services\School\TeacherSubstitutionService;
use Tests\TestCase;

class TeacherSubstitutionServiceTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    public function test_mark_leave_assigna_data_de_baixa(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P100',
            'activo' => 1,
            'rol' => config('roles.rol.profesor'),
            'fecha_baja' => null,
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        (new TeacherSubstitutionService())->markLeave('P100', '2026-02-12');

        $fecha = (string) DB::table('profesores')->where('dni', 'P100')->value('fecha_baja');
        $this->assertStringStartsWith('2026-02-12', $fecha);
    }

    public function test_reactivate_traspassa_dades_del_substitut_i_el_desactiva(): void
    {
        DB::table('profesores')->insert([
            [
                'dni' => 'P200',
                'activo' => 1,
                'rol' => config('roles.rol.profesor'),
                'fecha_baja' => '2026-02-10',
                'sustituye_a' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'P201',
                'activo' => 1,
                'rol' => config('roles.rol.profesor'),
                'fecha_baja' => null,
                'sustituye_a' => 'P200',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('horarios')->insert([
            'idProfesor' => 'P201',
        ]);
        DB::table('reuniones')->insert([
            'id' => 1,
            'idProfesor' => 'P201',
        ]);
        DB::table('asistencias')->insert([
            'idReunion' => 1,
            'idProfesor' => 'P201',
            'asiste' => 1,
        ]);
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'tutor' => 'P201',
        ]);
        DB::table('programaciones')->insert([
            'profesor' => 'P201',
        ]);
        DB::table('expedientes')->insert([
            'idProfesor' => 'P201',
        ]);
        DB::table('resultados')->insert([
            'idProfesor' => 'P201',
        ]);
        DB::table('alumno_fcts')->insert([
            'idProfesor' => 'P201',
        ]);

        (new TeacherSubstitutionService())->reactivate('P200');

        $this->assertNull(DB::table('profesores')->where('dni', 'P200')->value('fecha_baja'));
        $this->assertSame(0, (int) DB::table('profesores')->where('dni', 'P201')->value('activo'));
        $this->assertSame(' ', DB::table('profesores')->where('dni', 'P201')->value('sustituye_a'));

        $this->assertSame(1, DB::table('horarios')->where('idProfesor', 'P200')->count());
        $this->assertSame(1, DB::table('reuniones')->where('idProfesor', 'P200')->count());
        $this->assertSame(1, DB::table('grupos')->where('tutor', 'P200')->count());
        $this->assertSame(1, DB::table('programaciones')->where('profesor', 'P200')->count());
        $this->assertSame(1, DB::table('expedientes')->where('idProfesor', 'P200')->count());
        $this->assertSame(1, DB::table('resultados')->where('idProfesor', 'P200')->count());
        $this->assertSame(1, DB::table('alumno_fcts')->where('idProfesor', 'P200')->count());
        $this->assertSame(1, DB::table('asistencias')
            ->where('idProfesor', 'P200')
            ->where('idReunion', 1)
            ->count());
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->unsignedInteger('rol')->default(3);
            $table->boolean('activo')->default(1);
            $table->dateTime('fecha_baja')->nullable();
            $table->string('sustituye_a')->nullable();
            $table->timestamps();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor')->nullable();
            $table->timestamps();
        });

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor')->nullable();
            $table->timestamps();
        });

        $schema->create('asistencias', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->string('idProfesor');
            $table->unsignedTinyInteger('asiste')->default(0);
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('tutor')->nullable();
            $table->timestamps();
        });

        $schema->create('programaciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('profesor')->nullable();
            $table->timestamps();
        });

        $schema->create('expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor')->nullable();
            $table->timestamps();
        });

        $schema->create('resultados', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor')->nullable();
            $table->timestamps();
        });

        $schema->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor')->nullable();
            $table->timestamps();
        });
    }
}
