<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Contracte normalitzat d'una FE d'ITACA i de les seues estades.
 *
 * Una FE pot contindre més d'una estada en empresa. Esta classe no depén de
 * Selenium ni d'Eloquent perquè el lector i la persistència siguen separats.
 */
final class ItacaFctImportRecord
{
    /** @param list<ItacaFePlacement> $estades */
    private function __construct(
        public readonly string $nia,
        public readonly string $cicle,
        public readonly string $dataInici,
        public readonly string $dataFi,
        public readonly string $tipus,
        public readonly bool $autoritzacio,
        public readonly int $horesPrevistes,
        public readonly ?string $confirmacio,
        public readonly ?int $horesRealitzades,
        public readonly array $estades,
    ) {
    }

    /** @param array<string, mixed> $source */
    public static function fromItaca(array $source): self
    {
        $estades = $source['estades'] ?? null;
        if (!is_array($estades) || $estades === []) {
            throw new InvalidArgumentException("La FE d'ITACA ha de tindre almenys una estada.");
        }

        return new self(
            self::requiredText($source, 'nia'),
            self::requiredText($source, 'cicle'),
            self::normalizeDate($source, 'data_inici'),
            self::normalizeDate($source, 'data_fi'),
            self::requiredText($source, 'tipus'),
            self::normalizeAuthorization($source),
            self::normalizeHours($source, 'hores_previstes'),
            self::optionalText($source, 'confirmacio'),
            self::optionalHours($source, 'hores_realitzades'),
            array_map(
                static fn (mixed $estada): ItacaFePlacement => ItacaFePlacement::fromItaca(
                    is_array($estada) ? $estada : []
                ),
                array_values($estades)
            ),
        );
    }

    /** Retorna una clau idempotent per a una estada concreta. */
    public function identityKey(ItacaFePlacement $estada): string
    {
        return 'itaca-fe:' . hash('sha256', implode("\x1F", [
            $this->nia,
            self::keyText($this->cicle),
            self::keyText($estada->cifEmpresa),
            self::keyText($estada->centreTreball),
            self::keyText($estada->nifInstructor),
            $this->dataInici,
            $this->dataFi,
            self::keyText($this->tipus),
            (string) $this->horesPrevistes,
        ]));
    }

    /** @param array<string, mixed> $source */
    private static function requiredText(array $source, string $field): string
    {
        $value = self::optionalText($source, $field);
        if ($value === null) {
            throw new InvalidArgumentException("Falta el camp obligatori d'ITACA: $field.");
        }

        return $value;
    }

    /** @param array<string, mixed> $source */
    private static function optionalText(array $source, string $field): ?string
    {
        $value = trim((string) ($source[$field] ?? ''));

        return $value === '' ? null : preg_replace('/\s+/u', ' ', $value);
    }

    /** @param array<string, mixed> $source */
    private static function normalizeDate(array $source, string $field): string
    {
        $value = self::requiredText($source, $field);
        foreach (['Y-m-d', 'd/m/Y'] as $format) {
            $date = DateTimeImmutable::createFromFormat('!' . $format, $value);
            if ($date !== false && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        throw new InvalidArgumentException("La data d'ITACA no és vàlida: $field.");
    }

    /** @param array<string, mixed> $source */
    private static function normalizeAuthorization(array $source): bool
    {
        return match (mb_strtolower(self::requiredText($source, 'autoritzacio'), 'UTF-8')) {
            'sí', 'si', 'true', '1' => true,
            'no', 'false', '0' => false,
            default => throw new InvalidArgumentException("L'autorització d'ITACA no és vàlida."),
        };
    }

    /** @param array<string, mixed> $source */
    private static function normalizeHours(array $source, string $field): int
    {
        $value = self::requiredText($source, $field);
        if (!ctype_digit($value) || (int) $value <= 0) {
            throw new InvalidArgumentException("Les hores d'ITACA no són vàlides: $field.");
        }

        return (int) $value;
    }

    /** @param array<string, mixed> $source */
    private static function optionalHours(array $source, string $field): ?int
    {
        return self::optionalText($source, $field) === null ? null : self::normalizeHours($source, $field);
    }

    private static function keyText(string $value): string
    {
        return mb_strtolower(trim($value), 'UTF-8');
    }
}
