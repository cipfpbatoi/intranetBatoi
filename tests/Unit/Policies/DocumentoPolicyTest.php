<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Documento;
use Intranet\Policies\DocumentoPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de documents.
 */
class DocumentoPolicyTest extends TestCase
{
    public function test_create_permet_usuari_amb_dni(): void
    {
        $policy = new DocumentoPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
    }

    public function test_create_denega_usuari_invalid(): void
    {
        $policy = new DocumentoPolicy();

        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_view_update_delete_requerixen_identitat(): void
    {
        $policy = new DocumentoPolicy();
        $documento = new Documento();

        $user = (object) ['dni' => 'PRF002'];
        $invalid = (object) [];

        $this->assertTrue($policy->view($user, $documento));
        $this->assertTrue($policy->update($user, $documento));
        $this->assertTrue($policy->delete($user, $documento));

        $this->assertFalse($policy->view($invalid, $documento));
        $this->assertFalse($policy->update($invalid, $documento));
        $this->assertFalse($policy->delete($invalid, $documento));
    }
}
