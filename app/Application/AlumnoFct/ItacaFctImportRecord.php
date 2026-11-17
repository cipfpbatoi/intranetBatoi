<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Contracte normalitzat d'una formació en empresa llegida des d'ITACA SAO.
 *
 * Esta classe és deliberadament independent de Selenium, Playwright i
 * Eloquent. La persistència decidirà més avant on guardar la clau externa.
 */
final class ItacaFctImportRecord
{
    /**
     * @param array<string, mixed> $source
     */
    private function __construct(
        public readonly string $nia,
        public readonly string $cicle,
        public readonly string $centreTreball,
        public readonly string $tutor,
        public readonly string $dataInici,
        public readonly string $dataFi,
        public readonly string $tipus,
        public readonly bool $autoritzacio,
        public readonly int $horesPrevistes,
        public readonly ?string $confirmacio,
        public readonly ?int $horesRealitzades,
    ) {
    }

    /**
     * Normalitza la dada llegida del llistat FE d'ITACA SAO.
     *
     * @param array<string, mixed> $source
     */
    public static function fromItaca(array $source): self
    {
        return new self(
            self::requiredText($source, 'nia'),
            self::requiredText($source, 'cicle'),
            self::requiredText($source, 'centre_treball'),
            self::requiredText($source, 'tutor'),
            self::normalizeDate($source, 'data_inici'),
            self::normalizeDate($source, 'data_fi'),
            self::requiredText($source, 'tipus'),
            self::normalizeAuthorization($source),
            self::normalizeHours($source, 'hores_previstes'),
            self::optionalText($source, 'confirmacio'),
            self::optionalHours($source, 'hores_realitzades'),
        );
    }

    /**
     * Retorna una empremta determinista mentre ITACA no expose un ID de FE.
     *
     * La clau no substituïx `idSao`, que continua reservat als fluxos de
     * documents llegats. Una tasca posterior decidirà el seu emmagatzematge.
     */
    public function identityKey(): string
    {
        $parts = [
            $this->nia,
            $this->cicle,
            $this->centreTreball,
            $this->dataInici,
            $this->dataFi,
            $this->tipus,
            (string) $this->horesPrevistes,
        ];

        return 'itaca-fe:' . hash('sha256', implode("\x1F", $parts));
    }

    /**
     * @param array<string, mixed> $source
     */
    private static function requiredText(array $source, string $field): string
    {
        $value = self::optionalText($source, $field);
        if ($value === null) {
            throw new InvalidArgumentException("Falta el camp obligatori d'ITACA: $field.");
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $source
     */
    private static function optionalText(array $source, string $field): ?string
    {
        $value = trim((string) ($source[$field] ?? ''));
        if ($value === '') {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }

    /**
     * @param array<string, mixed> $source
     */
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

    /**
     * @param array<string, mixed> $source
     */
    private static function normalizeAuthorization(array $source): bool
    {
        $value = self::requiredText($source, 'autoritzacio');

        return match ($value) {
            'sí', 'si', 'true', '1' => true,
            'no', 'false', '0' => false,
            default => throw new InvalidArgumentException("L'autorització d'ITACA no és vàlida."),
        };
    }

    /**
     * @param array<string, mixed> $source
     */
    private static function normalizeHours(array $source, string $field): int
    {
        $value = self::requiredText($source, $field);
        if (!ctype_digit($value) || (int) $value <= 0) {
            throw new InvalidArgumentException("Les hores d'ITACA no són vàlides: $field.");
        }

        return (int) $value;
    }

    /**
     * @param array<string, mixed> $source
     */
    private static function optionalHours(array $source, string $field): ?int
    {
        if (self::optionalText($source, $field) === null) {
            return null;
        }

        return self::normalizeHours($source, $field);
    }
}
