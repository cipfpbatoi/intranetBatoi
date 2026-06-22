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
    public function test_create_permet_usuari_amb_dni_i_denega_invalid(): void
    {
        $policy = new ReunionPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_update_manage_i_notify_requerixen_ser_convocant(): void
    {
        $policy = new ReunionPolicy();
        $reunion = new Reunion();
        $reunion->idProfesor = 'PRF001';

        $owner = (object) ['dni' => 'PRF001'];
        $other = (object) ['dni' => 'PRF999'];

        $this->assertTrue($policy->update($owner, $reunion));
        $this->assertTrue($policy->manageParticipants($owner, $reunion));
        $this->assertTrue($policy->manageOrder($owner, $reunion));
        $this->assertTrue($policy->notify($owner, $reunion));

        $this->assertFalse($policy->update($other, $reunion));
        $this->assertFalse($policy->manageParticipants($other, $reunion));
        $this->assertFalse($policy->manageOrder($other, $reunion));
        $this->assertFalse($policy->notify($other, $reunion));
    }

    public function test_update_manage_i_notify_permeten_tutor_actual_del_grup(): void
    {
        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('profesores');
        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('sustituye_a', 10)->nullable();
        });
        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('tutor', 10)->nullable();
        });
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
