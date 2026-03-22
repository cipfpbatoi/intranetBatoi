<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\TutoriaGrupo;
use Intranet\Policies\TutoriaGrupoPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de tutoria-grup.
 */
class TutoriaGrupoPolicyTest extends TestCase
{
    public function test_create_view_update_delete_permeten_professor_amb_dni(): void
    {
        $policy = new TutoriaGrupoPolicy();
        $record = new TutoriaGrupo();

        $user = (object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($user));
        $this->assertTrue($policy->view($user, $record));
        $this->assertTrue($policy->update($user, $record));
        $this->assertTrue($policy->delete($user, $record));
        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
    }
}
