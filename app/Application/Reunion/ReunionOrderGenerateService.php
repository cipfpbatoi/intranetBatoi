<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;

/**
 * Genera els punts inicials d'una reunió des de plantilles tipades.
 */
class ReunionOrderGenerateService
{
    /**
     * @param ReunionContinuityService $continuityService
     * @param ReunionOrderTemplateProvider $templates
     * @param ReunionOrderResolverRegistry $resolvers
     */
    public function __construct(
        private ReunionContinuityService $continuityService,
        private ReunionOrderTemplateProvider $templates,
        private ReunionOrderResolverRegistry $resolvers
    ) {
    }

    /**
     * Crea tots els punts configurats i aplica la continuïtat per codi.
     */
    public function generate(Reunion $reunion): void
    {
        $position = 1;
        $inheritedSummaries = $this->continuityService->inheritedSummaries($reunion);

        foreach ($this->templates->for($reunion) as $template) {
            foreach ($this->resolvers->resolve($reunion, $template) as $draft) {
                $summary = $draft->code !== null && array_key_exists($draft->code, $inheritedSummaries)
                    ? $inheritedSummaries[$draft->code]
                    : $draft->summary;

                $order = new OrdenReunion();
                $order->idReunion = $reunion->id;
                $order->codigo = $draft->code;
                $order->orden = $position++;
                $order->descripcion = $draft->description;
                $order->resumen = $this->continuityService->normaliseSummary($summary);
                $order->save();
            }
        }
    }
}
