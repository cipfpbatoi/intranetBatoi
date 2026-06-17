<?php

namespace Tests\Unit\Console\Commands;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Intranet\Mail\AvalFct;
use Intranet\Mail\CertificatAlumneFct;
use Intranet\Mail\CertificatInstructorFct;
use Tests\TestCase;

/**
 * Proves del comandament diari d'enviament de certificats FCT.
 */
class SendFctEmailsTest extends TestCase
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

    public function test_no_envia_certificat_a_alumnat_i_envia_certificat_a_instructor(): void
    {
        Event::fake();
        Mail::fake();
        Notification::fake();
        $this->seedFct([
            ['id' => 100, 'idAlumno' => 'A1', 'calificacion' => 1, 'correoAlumno' => 0],
        ]);

        $result = Artisan::call('fct:Daily');

        $this->assertSame(0, $result);
        Mail::assertNotSent(CertificatAlumneFct::class);
        Mail::assertSent(AvalFct::class, 1);
        Mail::assertSent(CertificatInstructorFct::class, 1);
        $this->assertSame(0, (int) DB::table('alumno_fcts')->where('id', 100)->value('correoAlumno'));
        $this->assertSame(1, (int) DB::table('fcts')->where('id', 10)->value('correoInstructor'));
    }

    public function test_no_envia_certificat_a_instructor_si_queda_alumnat_sense_qualificar(): void
    {
        Event::fake();
        Mail::fake();
        Notification::fake();
        $this->seedFct([
            ['id' => 100, 'idAlumno' => 'A1', 'calificacion' => 1, 'correoAlumno' => 0],
            ['id' => 101, 'idAlumno' => 'A2', 'calificacion' => null, 'correoAlumno' => 0],
        ]);

        $result = Artisan::call('fct:Daily');

        $this->assertSame(0, $result);
        Mail::assertNotSent(CertificatAlumneFct::class);
        Mail::assertNotSent(AvalFct::class);
        Mail::assertNotSent(CertificatInstructorFct::class);
        $this->assertSame(0, (int) DB::table('fcts')->where('id', 10)->value('correoInstructor'));
    }

    /**
     * Crea una FCT compartida amb instructor, tutor i alumnat associat.
     *
     * @param array<int, array{id:int,idAlumno:string,calificacion:int|null,correoAlumno:int}> $alumnosFct
     */
    private function seedFct(array $alumnosFct): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P1',
            'nombre' => 'Tutora',
            'apellido1' => 'FCT',
            'apellido2' => '',
            'email' => 'tutora@example.test',
        ]);

        DB::table('instructores')->insert([
            'dni' => 'I1',
            'email' => 'instructor@example.test',
            'name' => 'Instructor',
            'surnames' => 'Empresa',
        ]);

        DB::table('colaboraciones')->insert([
            'id' => 200,
            'tutor' => 'P1',
        ]);

        DB::table('fcts')->insert([
            'id' => 10,
            'idColaboracion' => 200,
            'idInstructor' => 'I1',
            'correoInstructor' => 0,
            'asociacion' => 1,
        ]);

        foreach ($alumnosFct as $alumnoFct) {
            DB::table('alumnos')->insert([
                'nia' => $alumnoFct['idAlumno'],
                'dni' => $alumnoFct['idAlumno'],
                'nombre' => 'Alumne',
                'apellido1' => $alumnoFct['idAlumno'],
                'apellido2' => '',
                'email' => strtolower($alumnoFct['idAlumno']).'@example.test',
            ]);

            DB::table('alumno_fcts')->insert([
                'id' => $alumnoFct['id'],
                'idAlumno' => $alumnoFct['idAlumno'],
                'idFct' => 10,
                'idProfesor' => 'P1',
                'calificacion' => $alumnoFct['calificacion'],
                'correoAlumno' => $alumnoFct['correoAlumno'],
            ]);
        }
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('alumno_fcts');
        $schema->dropIfExists('fcts');
        $schema->dropIfExists('colaboraciones');
        $schema->dropIfExists('instructores');
        $schema->dropIfExists('profesores');
        $schema->dropIfExists('alumnos');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('dni')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
        });

        $schema->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('surnames')->nullable();
        });

        $schema->create('colaboraciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tutor')->nullable();
        });

        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idColaboracion')->nullable();
            $table->string('idInstructor')->nullable();
            $table->string('cotutor')->nullable();
            $table->unsignedTinyInteger('correoInstructor')->default(0);
            $table->unsignedTinyInteger('asociacion')->default(1);
        });

        $schema->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumno')->nullable();
            $table->unsignedInteger('idFct')->nullable();
            $table->string('idProfesor')->nullable();
            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->unsignedTinyInteger('correoAlumno')->default(0);
        });
    }
}
