<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion\OrderResolvers;

use Intranet\Application\Reunion\ReunionOrderDraft;
use Intranet\Application\Reunion\ReunionOrderResolver;
use Intranet\Application\Reunion\ReunionOrderStudentQuery;
use Intranet\Application\Reunion\ReunionOrderTemplate;
use Intranet\Entities\Reunion;

/**
 * Genera un punt per cada alumne LOE amb proposta de projecte.
 */
class LoeProjectStudentsOrderResolver implements ReunionOrderResolver
{
    public const KEY = 'loe_project_students';

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
     * Resol un punt numerat per cada alumne recuperat.
     *
     * @return array<int, ReunionOrderDraft>
     */
    public function resolve(Reunion $reunion, ReunionOrderTemplate $template): array
    {
        return $this->students->loeStudents((string) $reunion->idProfesor)
            ->map(static fn (string $name, int $index): ReunionOrderDraft => new ReunionOrderDraft(
                $template->code,
                $name,
                $template->summary . ' ' . ($index + 1)
            ))
            ->all();
    }
}
