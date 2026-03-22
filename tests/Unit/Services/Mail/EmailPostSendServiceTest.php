<?php

namespace Tests\Unit\Services\Mail;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Intranet\Entities\Signatura;
use Intranet\Services\Mail\EmailPostSendService;
use Tests\TestCase;

class EmailPostSendServiceTest extends TestCase
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

    public function test_handle_annexe_individual_actualitza_signatura(): void
    {
        DB::table('signatures')->insert([
            'id' => 1,
            'idSao' => 'SAO1',
            'tipus' => 'A1',
            'idProfesor' => 'P1',
            'sendTo' => 0,
            'signed' => 0,
        ]);

        $signatura = Signatura::findOrFail(1);

        (new EmailPostSendService())->handleAnnexeIndividual($signatura);

        $this->assertSame(2, (int) DB::table('signatures')->where('id', 1)->value('sendTo'));
    }

    public function test_handle_annexe_individual_actualitza_signatures_de_alumno_fct(): void
    {
        DB::table('alumno_fcts')->insert([
            'id' => 1,
            'idSao' => 'SAO2',
        ]);

        DB::table('signatures')->insert([
            [
                'id' => 2,
                'idSao' => 'SAO2',
                'tipus' => 'A2',
                'idProfesor' => 'P2',
                'sendTo' => 1,
                'signed' => 0,
            ],
            [
                'id' => 3,
                'idSao' => 'SAO2',
                'tipus' => 'A3',
                'idProfesor' => 'P2',
                'sendTo' => 0,
                'signed' => 0,
            ],
        ]);

        $alumnoFct = AlumnoFct::findOrFail(1);

        (new EmailPostSendService())->handleAnnexeIndividual($alumnoFct);

        $this->assertSame(3, (int) DB::table('signatures')->where('id', 2)->value('sendTo'));
        $this->assertSame(2, (int) DB::table('signatures')->where('id', 3)->value('sendTo'));
    }

    public function test_mark_fct_email_sent_actualitza_flags(): void
    {
        DB::table('fcts')->insert([
            'id' => 1,
            'correoInstructor' => 0,
            'correoAlumno' => 0,
        ]);

        DB::table('instructores')->insert([
            'dni' => 'I1',
            'email' => 'test@example.com',
        ]);

        DB::table('alumnos')->insert([
            'nia' => 'A1',
            'email' => 'test@example.com',
        ]);

        $fct = Fct::findOrFail(1);

        (new EmailPostSendService())->markFctEmailSent($fct, 'test@example.com');

        $this->assertSame(1, (int) DB::table('fcts')->where('id', 1)->value('correoInstructor'));
        $this->assertSame(1, (int) DB::table('fcts')->where('id', 1)->value('correoAlumno'));
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('signatures');
        $schema->dropIfExists('alumno_fcts');
        $schema->dropIfExists('fcts');
        $schema->dropIfExists('instructores');
        $schema->dropIfExists('alumnos');

        $schema->create('signatures', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idSao')->nullable();
            $table->string('tipus')->nullable();
            $table->string('idProfesor')->nullable();
            $table->unsignedTinyInteger('sendTo')->default(0);
            $table->unsignedTinyInteger('signed')->default(0);
            $table->timestamps();
        });

        $schema->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idSao')->nullable();
        });

        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('correoInstructor')->default(0);
            $table->unsignedTinyInteger('correoAlumno')->default(0);
        });

        $schema->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('email')->nullable();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('email')->nullable();
        });
    }
}
