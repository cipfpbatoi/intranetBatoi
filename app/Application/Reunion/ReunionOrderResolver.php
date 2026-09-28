<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Intranet\Entities\Reunion;

/**
 * Contracte dels resolutors explícits de punts inicials.
 */
interface ReunionOrderResolver
{
    /**
     * Identificador estable del resolutor dins de les plantilles.
     */
    public function key(): string;

    /**
     * Resol una plantilla en un o més punts persistibles.
     *
     * @return array<int, ReunionOrderDraft>
     */
    public function resolve(Reunion $reunion, ReunionOrderTemplate $template): array;
}
