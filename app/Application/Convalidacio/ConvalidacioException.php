<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use DomainException;

/**
 * Error de negoci amb un motiu concret del domini de convalidacions.
 */
class ConvalidacioException extends DomainException
{
}
