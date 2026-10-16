<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion\OrderResolvers;

use Intranet\Application\Reunion\ReunionOrderDraft;
use Intranet\Application\Reunion\ReunionOrderResolver;
use Intranet\Application\Reunion\ReunionOrderStudentQuery;
use Intranet\Application\Reunion\ReunionOrderTemplate;
use Intranet\Entities\Reunion;

/**
 * Preompli el seguiment amb l'alumnat amb dificultats del tutor.
 */
class LearningDifficultiesOrderResolver implements ReunionOrderResolver
{
    public const KEY = 'learning_difficulties_students';

    /**
     * @param ReunionOrderStudentQuery $students
     */
    public function __construct(private ReunionOrderStudentQuery $students)
    {
    }

    /**
     * Retorna la clau del resolutor.
     */
    public function key(): string
    {
        return self::KEY;
    }

    /**
     * Resol un únic punt amb els noms de l'alumnat afectat.
     *
     * @return array<int, ReunionOrderDraft>
     */
    public function resolve(Reunion $reunion, ReunionOrderTemplate $template): array
    {
        return [new ReunionOrderDraft(
            $template->code,
            (string) $template->description,
            $this->students->learningDifficulties((string) $reunion->idProfesor)->implode(', ')
        )];
    }
}
