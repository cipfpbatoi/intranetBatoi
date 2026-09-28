<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

/**
 * Dades validades necessàries per a crear una reunió completa.
 */
final readonly class CreateReunionData
{
    public function __construct(
        public int $tipo,
        public ?string $grupo,
        public ?string $idGrupo,
        public string $curso,
        public ?int $numero,
        public string $fecha,
        public string $descripcion,
        public ?string $objetivos,
        public string $idEspacio
    ) {
    }

    /**
     * Construïx l'entrada tipada a partir de dades ja validades.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['tipo'],
            self::nullableString($data['grupo'] ?? null),
            self::nullableString($data['idGrupo'] ?? null),
            (string) $data['curso'],
            isset($data['numero']) ? (int) $data['numero'] : null,
            (string) $data['fecha'],
            (string) $data['descripcion'],
            self::nullableString($data['objetivos'] ?? null),
            (string) $data['idEspacio']
        );
    }

    /**
     * Normalitza un valor opcional de text.
     */
    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
