<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Reunion;
use Intranet\Policies\ReunionPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de l'autorització de reunions.
 */
class ReunionPolicyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('asistencias');
        $schema->dropIfExists('reuniones');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('profesores');
        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('rol')->default(3);
            $table->string('sustituye_a', 10)->nullable();
        });
        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('tutor', 10)->nullable();
        });
        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('idGrupo', 10)->nullable();
            $table->boolean('archivada')->default(false);
            $table->timestamps();
        });
        $schema->create('asistencias', function (Blueprint $table): void {
            $table->unsignedInteger('idReunion');
            $table->string('idProfesor', 10);
            $table->boolean('asiste')->default(false);
        });
    }

    public function test_create_permet_usuari_amb_dni_i_denega_invalid(): void
    {
        $policy = new ReunionPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_update_manage_notify_i_archive_requerixen_ser_convocant(): void
    {
        $policy = new ReunionPolicy();
        $reunion = new Reunion();
        $reunion->idProfesor = 'PRF001';

        $owner = (object) ['dni' => 'PRF001'];
        $other = (object) ['dni' => 'PRF999'];

        $this->assertTrue($policy->update($owner, $reunion));
        $this->assertTrue($policy->delete($owner, $reunion));
        $this->assertTrue($policy->manageParticipants($owner, $reunion));
        $this->assertTrue($policy->manageOrder($owner, $reunion));
        $this->assertTrue($policy->notify($owner, $reunion));
        $this->assertTrue($policy->archive($owner, $reunion));

        $this->assertFalse($policy->update($other, $reunion));
        $this->assertFalse($policy->delete($other, $reunion));
        $this->assertFalse($policy->manageParticipants($other, $reunion));
        $this->assertFalse($policy->manageOrder($other, $reunion));
        $this->assertFalse($policy->notify($other, $reunion));
        $this->assertFalse($policy->archive($other, $reunion));
    }

    public function test_update_manage_notify_i_archive_permeten_tutor_actual_del_grup(): void
    {
        DB::table('profesores')->insert(['dni' => 'PRF999', 'sustituye_a' => null]);
        DB::table('grupos')->insert(['codigo' => 'G1', 'tutor' => 'PRF999']);

        $policy = new ReunionPolicy();
        $reunion = new Reunion();
        $reunion->idProfesor = 'PRF001';
        $reunion->idGrupo = 'G1';
        $tutorActual = (object) ['dni' => 'PRF999'];

        $this->assertTrue($policy->update($tutorActual, $reunion));
        $this->assertTrue($policy->manageParticipants($tutorActual, $reunion));
        $this->assertTrue($policy->manageOrder($tutorActual, $reunion));
        $this->assertTrue($policy->notify($tutorActual, $reunion));
        $this->assertTrue($policy->archive($tutorActual, $reunion));
    }

    public function test_view_permet_convocant_assistent_i_direccio_pero_no_aliens(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'PRF001', 'rol' => config('roles.rol.profesor')],
            ['dni' => 'PRF002', 'rol' => config('roles.rol.profesor')],
            ['dni' => 'PRF003', 'rol' => config('roles.rol.profesor')],
        ]);
        DB::table('reuniones')->insert([
            'id' => 10,
            'idProfesor' => 'PRF001',
            'archivada' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('asistencias')->insert([
            'idReunion' => 10,
            'idProfesor' => 'PRF002',
            'asiste' => true,
        ]);

        $policy = new ReunionPolicy();
        $reunion = Reunion::query()->findOrFail(10);

        $this->assertTrue($policy->view((object) ['dni' => 'PRF001', 'rol' => 3], $reunion));
        $this->assertTrue($policy->view((object) ['dni' => 'PRF002', 'rol' => 3], $reunion));
        $this->assertTrue($policy->view((object) ['dni' => 'DIR001', 'rol' => 6], $reunion));
        $this->assertFalse($policy->view((object) ['dni' => 'PRF003', 'rol' => 3], $reunion));
    }

    public function test_acta_arxivada_es_immutable_i_només_es_pot_desarxivar_explicitament(): void
    {
        $policy = new ReunionPolicy();
        $reunion = new Reunion();
        $reunion->idProfesor = 'PRF001';
        $reunion->archivada = true;
        $owner = (object) ['dni' => 'PRF001'];

        $this->assertFalse($policy->update($owner, $reunion));
        $this->assertFalse($policy->delete($owner, $reunion));
        $this->assertFalse($policy->manageParticipants($owner, $reunion));
        $this->assertFalse($policy->manageOrder($owner, $reunion));
        $this->assertFalse($policy->archive($owner, $reunion));
        $this->assertTrue($policy->unarchive($owner, $reunion));
        $this->assertFalse($policy->unarchive((object) ['dni' => 'PRF999'], $reunion));
    }

    public function test_manage_department_report_requerix_rol_cap_de_departament(): void
    {
        $policy = new ReunionPolicy();

        $capDepartament = (object) ['rol' => (int) config('roles.rol.jefe_dpto')];
        $professor = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->manageDepartmentReport($capDepartament));
        $this->assertFalse($policy->manageDepartmentReport($professor));
        $this->assertFalse($policy->manageDepartmentReport((object) []));
        $this->assertFalse($policy->manageDepartmentReport(null));
    }
}
