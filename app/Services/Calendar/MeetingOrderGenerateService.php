<?php

declare(strict_types=1);

namespace Intranet\Services\Calendar;

use Intranet\Application\Reunion\ReunionContinuityService;
use Intranet\Application\Reunion\ReunionOrderGenerateService;
use Intranet\Application\Reunion\ReunionOrderResolverRegistry;
use Intranet\Application\Reunion\ReunionOrderTemplateProvider;
use Intranet\Entities\Reunion;

/**
 * Façana compatible del generador de punts traslladat a Application/Reunion.
 */
class MeetingOrderGenerateService
{
    public function __construct(
        private Reunion $reunion,
        private ?ReunionContinuityService $continuityService = null,
        private ?ReunionOrderGenerateService $generator = null
    ) {
    }

    /**
     * Crea tots els punts configurats per al tipus de reunió.
     */
    public function exec(): void
    {
        $this->generator()->generate($this->reunion);
    }

    /**
     * Resol el generador nou mantenint la injecció llegada del servei de continuïtat.
     */
    private function generator(): ReunionOrderGenerateService
    {
        if ($this->generator !== null) {
            return $this->generator;
        }

        if ($this->continuityService === null) {
            return app(ReunionOrderGenerateService::class);
        }

        return new ReunionOrderGenerateService(
            $this->continuityService,
            app(ReunionOrderTemplateProvider::class),
            app(ReunionOrderResolverRegistry::class)
        );
    }
}
