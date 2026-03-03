<?php

namespace Intranet\Exceptions;

use Throwable;

/**
 * Excepció de domini per a recursos no trobats (404).
 */
class NotFoundDomainException extends IntranetException
{
    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'Element no trobat',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 404, $message, false, $context, $previous);
    }
}
