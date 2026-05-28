<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Signatura;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class SignaturaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('signatures');
        $schema->dropIfExists('profesores');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('api_token', 80)->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
            $table->unsignedInteger('rol')->default((int) config('roles.rol.profesor'));
            $table->timestamps();
        });

        $schema->create('signatures', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipus', 5);
            $table->string('idProfesor', 10)->nullable();
            $table->integer('idSao');
            $table->unsignedTinyInteger('sendTo')->default(0);
            $table->unsignedTinyInteger('signed')->default(0);
            $table->timestamps();
        });

        DB::table('profesores')->insert([
            'dni' => 'P100',
            'api_token' => 'token-signatura-test',
            'activo' => 1,
            'rol' => (int) config('roles.rol.profesor'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_save_if_not_exists_crea_i_actualitza_sense_duplicar(): void
    {
        $this->be(Profesor::query()->findOrFail('P100'));

        $first = Signatura::saveIfNotExists('A1', 1001, 0);
        $this->assertSame(1, Signatura::query()->where('tipus', 'A1')->where('idSao', 1001)->count());
        $this->assertSame(0, (int) $first->signed);
        $this->assertSame(0, (int) $first->sendTo);

        DB::table('signatures')->where('id', $first->id)->update(['sendTo' => 1]);
        $second = Signatura::saveIfNotExists('A1', 1001, 2);

        $this->assertSame(1, Signatura::query()->where('tipus', 'A1')->where('idSao', 1001)->count());
        $this->assertSame($first->id, $second->id);
        $this->assertSame(2, (int) $second->signed);
        $this->assertSame(0, (int) $second->sendTo);
    }

    public function test_estat_a5_i_tipus_desconegut(): void
    {
        $a5 = new Signatura([
            'tipus' => 'A5',
            'signed' => 3,
            'sendTo' => 1,
        ]);
        $this->assertSame('Complet', $a5->estat);

        $unknown = new Signatura([
            'tipus' => 'ZZ',
            'signed' => 0,
            'sendTo' => 0,
        ]);
        $this->assertSame('Tipus desconegut', $unknown->estat);
    }

    public function test_accessors_relacionals_son_null_safe(): void
    {
        $sig = new Signatura([
            'tipus' => 'A1',
            'idSao' => 10,
            'signed' => 0,
            'sendTo' => 0,
        ]);
        $sig->setRelation('Teacher', null);
        $sig->setRelation('Fct', null);

        $this->assertSame('', $sig->profesor);
        $this->assertSame('', $sig->alumne);
        $this->assertSame('', $sig->centre);
        $this->assertSame('', $sig->email);
        $this->assertSame('', $sig->contacto);
    }

    public function test_class_accessor_casos_principals(): void
    {
        $orange = new Signatura(['tipus' => 'A3', 'sendTo' => 1, 'signed' => 2]);
        $this->assertSame('bg-orange', $orange->class);

        $green = new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 3]);
        $this->assertSame('bg-green', $green->class);

        $red = new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 1]);
        $this->assertSame('bg-red', $red->class);
    }

    public function test_estat_a1_a2_a3_casos_principals(): void
    {
        $a1Pending = new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 2]);
        $this->assertSame('Pendent Signatura Direcció', $a1Pending->estat);
        $a1Done = new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 3]);
        $this->assertSame('Signatura Direcció completada', $a1Done->estat);

        $a2Pending = new Signatura(['tipus' => 'A2', 'sendTo' => 0, 'signed' => 1]);
        $this->assertSame('Pendent de Signatura Direcció', $a2Pending->estat);
        $a2Done = new Signatura(['tipus' => 'A2', 'sendTo' => 0, 'signed' => 4]);
        $this->assertSame('Signatura Direcció completada', $a2Done->estat);

        $a3ToStudent = new Signatura(['tipus' => 'A3', 'sendTo' => 1, 'signed' => 2]);
        $this->assertSame("Enviat a l'alumne", $a3ToStudent->estat);
        $a3ToInstructorWithoutStudent = new Signatura(['tipus' => 'A3', 'sendTo' => 2, 'signed' => 2]);
        $this->assertSame("Enviat a l'instructor sense la signatura de l'alumne", $a3ToInstructorWithoutStudent->estat);
    }

    public function test_sign_send_i_tipus_options(): void
    {
        $signed = new Signatura(['tipus' => 'A1', 'sendTo' => 1, 'signed' => 1]);
        $this->assertSame('Sí', $signed->sign);
        $this->assertSame('Sí', $signed->send);

        $unsigned = new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 0]);
        $this->assertSame('No', $unsigned->sign);
        $this->assertSame('No', $unsigned->send);

        $this->assertSame(
            ['A1' => 'A1', 'A2' => 'A2', 'A3' => 'A3', 'A5' => 'A5'],
            $unsigned->getTipusOptions()
        );
    }
}
