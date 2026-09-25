<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

/**
 * Resultat explícit del cas d'ús d'arxivament d'una reunió.
 */
class ReunionArchiveResult
{
    private const ARCHIVED = 'archived';
    private const ALREADY_ARCHIVED = 'already_archived';

    /**
     * @param string $status
     */
    private function __construct(private string $status)
    {
    }

    /**
     * Indica que l'acta s'ha arxivat correctament.
     */
    public static function archived(): self
    {
        return new self(self::ARCHIVED);
    }

    /**
     * Indica que l'acta ja estava arxivada i no s'ha modificat.
     */
    public static function alreadyArchived(): self
    {
        return new self(self::ALREADY_ARCHIVED);
    }

    /**
     * Comprova si no ha calgut repetir l'arxivament.
     */
    public function wasAlreadyArchived(): bool
    {
        return $this->status === self::ALREADY_ARCHIVED;
    }
}
