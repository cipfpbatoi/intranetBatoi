<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Intranet\Application\Reunion\OrderResolvers\LearningDifficultiesOrderResolver;
use Intranet\Application\Reunion\OrderResolvers\LoeProjectStudentsOrderResolver;
use Intranet\Application\Reunion\OrderResolvers\ProjectDefenseStudentsOrderResolver;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;

/**
 * Transforma la configuració de tipus de reunió en plantilles tipades.
 */
class ReunionOrderTemplateProvider
{
    /**
     * Retorna les plantilles inicials del tipus de reunió indicat.
     *
     * @return array<int, ReunionOrderTemplate>
     */
    public function for(Reunion $reunion): array
    {
        $features = $reunion->Tipos()->get();
        $orders = $features['ordenes'] ?? [];
        $legacySummaries = $features['resumen'] ?? '';
        $templates = [];

        foreach ($orders as $index => $configuredOrder) {
            $legacySummary = is_array($legacySummaries)
                ? ($legacySummaries[$index] ?? '')
                : $legacySummaries;

            $templates[] = is_array($configuredOrder)
                ? $this->fromStructuredConfiguration($configuredOrder)
                : $this->fromLegacyConfiguration((string) $configuredOrder, (string) $legacySummary);
        }

        return $templates;
    }

    /**
     * Crea una plantilla des de la nova configuració declarativa.
     *
     * @param array<string, mixed> $configuration
     */
    private function fromStructuredConfiguration(array $configuration): ReunionOrderTemplate
    {
        return new ReunionOrderTemplate(
            isset($configuration['code']) ? (string) $configuration['code'] : null,
            isset($configuration['description']) ? (string) $configuration['description'] : null,
            isset($configuration['summary']) ? (string) $configuration['summary'] : '',
            isset($configuration['resolver']) ? (string) $configuration['resolver'] : null
        );
    }

    /**
     * Adapta únicament les tres expressions llegades conegudes sense executar DSL.
     */
    private function fromLegacyConfiguration(string $description, string $summary): ReunionOrderTemplate
    {
        if ($description === 'Alumno->misLOE->FullName') {
            return new ReunionOrderTemplate(
                OrdenReunion::CODE_PROJECT_PROPOSAL_STUDENT,
                null,
                $summary,
                LoeProjectStudentsOrderResolver::KEY
            );
        }

        if ($description === 'AlumnoFct->misProyectos->FullName') {
            return new ReunionOrderTemplate(
                OrdenReunion::CODE_PROJECT_DEFENSE_STUDENT,
                null,
                $summary,
                ProjectDefenseStudentsOrderResolver::KEY
            );
        }

        $resolver = $summary === 'Alumno->misDA->fullName'
            ? LearningDifficultiesOrderResolver::KEY
            : null;

        return new ReunionOrderTemplate(
            OrdenReunion::codeForDescription($description),
            $description,
            $resolver === null ? $summary : '',
            $resolver
        );
    }
}
