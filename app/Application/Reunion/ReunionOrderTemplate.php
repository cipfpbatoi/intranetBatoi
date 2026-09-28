<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

/**
 * Plantilla tipada d'un punt inicial d'una reunió.
 */
class ReunionOrderTemplate
{
    /**
     * @param string|null $code
     * @param string|null $description
     * @param string $summary
     * @param string|null $resolver
     */
    public function __construct(
        public readonly ?string $code,
        public readonly ?string $description,
        public readonly string $summary = '',
        public readonly ?string $resolver = null
    ) {
    }
}
