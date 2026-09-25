<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Intranet\Application\Reunion\OrderResolvers\LearningDifficultiesOrderResolver;
use Intranet\Application\Reunion\OrderResolvers\LoeProjectStudentsOrderResolver;
use Intranet\Application\Reunion\OrderResolvers\ProjectDefenseStudentsOrderResolver;
use Intranet\Entities\Reunion;
use InvalidArgumentException;

/**
 * Registre tancat dels resolutors permesos per a punts de reunió.
 */
class ReunionOrderResolverRegistry
{
    /** @var array<string, ReunionOrderResolver> */
    private array $resolvers;

    /**
     * @param LearningDifficultiesOrderResolver $learningDifficulties
     * @param LoeProjectStudentsOrderResolver $loeProjects
     * @param ProjectDefenseStudentsOrderResolver $projectDefenses
     */
    public function __construct(
        LearningDifficultiesOrderResolver $learningDifficulties,
        LoeProjectStudentsOrderResolver $loeProjects,
        ProjectDefenseStudentsOrderResolver $projectDefenses
    ) {
        $this->resolvers = [
            $learningDifficulties->key() => $learningDifficulties,
            $loeProjects->key() => $loeProjects,
            $projectDefenses->key() => $projectDefenses,
        ];
    }

    /**
     * Resol una plantilla estàtica o delega en el resolutor registrat.
     *
     * @return array<int, ReunionOrderDraft>
     */
    public function resolve(Reunion $reunion, ReunionOrderTemplate $template): array
    {
        if ($template->resolver === null) {
            return [new ReunionOrderDraft(
                $template->code,
                (string) $template->description,
                $template->summary
            )];
        }

        $resolver = $this->resolvers[$template->resolver] ?? null;
        if ($resolver === null) {
            throw new InvalidArgumentException("Resolutor de punts desconegut: {$template->resolver}");
        }

        return $resolver->resolve($reunion, $template);
    }
}
