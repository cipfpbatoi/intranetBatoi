<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Articulo;
use Intranet\Policies\ArticuloPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy d'articles.
 */
class ArticuloPolicyTest extends TestCase
{
    public function test_mutacions_i_vista_per_usuari_amb_dni(): void
    {
        $policy = new ArticuloPolicy();
        $articulo = new Articulo();

        $professor = (object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->view($professor, $articulo));
        $this->assertTrue($policy->create($professor));
        $this->assertTrue($policy->update($professor, $articulo));
        $this->assertTrue($policy->delete($professor, $articulo));

        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
    }
}
