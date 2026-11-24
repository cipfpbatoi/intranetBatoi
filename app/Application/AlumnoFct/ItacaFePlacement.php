<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

use InvalidArgumentException;

/** Dades d'una estada empresa-centre-instructor dins d'una FE d'ITACA. */
final class ItacaFePlacement
{
    private function __construct(
        public readonly string $cifEmpresa,
        public readonly string $centreTreball,
        public readonly string $nifInstructor,
    ) {
    }

    /** @param array<string, mixed> $source */
    public static function fromItaca(array $source): self
    {
        return new self(
            self::required($source, 'cif_empresa'),
            self::required($source, 'centre_treball'),
            self::required($source, 'nif_instructor'),
        );
    }

    /** @param array<string, mixed> $source */
    private static function required(array $source, string $field): string
    {
        $value = trim((string) ($source[$field] ?? ''));
        if ($value === '') {
            throw new InvalidArgumentException("Falta el camp obligatori de l'estada ITACA: $field.");
        }

        return preg_replace('/\s+/u', ' ', $value) ?? $value;
    }
}
