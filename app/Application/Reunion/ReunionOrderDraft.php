<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

/**
 * Punt resolt i preparat per a persistir-lo en una reunió.
 */
class ReunionOrderDraft
{
    /**
     * @param string|null $code
     * @param string $description
     * @param string $summary
     */
    public function __construct(
        public readonly ?string $code,
        public readonly string $description,
        public readonly string $summary
    ) {
    }
}
