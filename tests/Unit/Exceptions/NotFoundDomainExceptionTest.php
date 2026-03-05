<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Intranet\Exceptions\NotFoundDomainException;
use PHPUnit\Framework\TestCase;

/**
 * Proves unitàries de la classe NotFoundDomainException.
 */
class NotFoundDomainExceptionTest extends TestCase
{
    /**
     * Verifica que l'excepció configura correctament estat, missatge i context.
     */
    public function test_constructor_configura_estat_i_context(): void
    {
        $exception = new NotFoundDomainException('Professor no trobat', ['profesor_id' => 'X123']);

        $this->assertSame('Professor no trobat', $exception->getMessage());
        $this->assertSame(404, $exception->getStatus());
        $this->assertSame('Professor no trobat', $exception->getUserMessage());
        $this->assertFalse($exception->shouldNotify());
        $this->assertSame(['profesor_id' => 'X123'], $exception->getContext());
    }
}

