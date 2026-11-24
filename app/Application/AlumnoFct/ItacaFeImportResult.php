<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

/** Resultat llegible de la importació d'una FE d'ITACA. */
final class ItacaFeImportResult
{
    /** @var list<string> */
    private array $avisos = [];

    public int $creades = 0;
    public int $existents = 0;
    public int $pendents = 0;

    public function addPending(string $aviso): void
    {
        $this->pendents++;
        $this->avisos[] = $aviso;
    }

    /** @return list<string> */
    public function avisos(): array
    {
        return $this->avisos;
    }
}
