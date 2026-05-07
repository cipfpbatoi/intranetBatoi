<?php

declare(strict_types=1);

namespace Intranet\Application\Empresa;

use Intranet\Entities\Centro;
use Intranet\Entities\Empresa;

/**
 * Actualitza empreses i centres amb dades SAO sense sobreescriure camps informats.
 */
class SaoCompanyDataUpdater
{
    /**
     * Ompli camps buits d'empresa i centre a partir d'una lectura SAO.
     *
     * @param Empresa $empresa
     * @param Centro $centro
     * @param array<string, mixed> $data
     * @param bool $dryRun
     * @return array{empresa:int, centro:int}
     */
    public function fillMissing(Empresa $empresa, Centro $centro, array $data, bool $dryRun = false): array
    {
        $empresaChanges = $this->fillMissingEmpresa($empresa, $data['empresa'] ?? [], $dryRun);
        $centroChanges = $this->fillMissingCentro($centro, $data['centre'] ?? [], $dryRun);

        return [
            'empresa' => $empresaChanges,
            'centro' => $centroChanges,
        ];
    }

    /**
     * Ompli camps buits d'una empresa.
     *
     * @param Empresa $empresa
     * @param array<string, mixed> $data
     * @param bool $dryRun
     * @return int
     */
    public function fillMissingEmpresa(Empresa $empresa, array $data, bool $dryRun = false): int
    {
        $fields = [
            'idSao',
            'concierto',
            'cif',
            'nombre',
            'direccion',
            'localidad',
            'telefono',
            'gerente',
            'actividad',
            'email',
            'data_signatura',
        ];

        $changes = $this->fillMissingFields($empresa, $fields, $data);

        if (!$dryRun && $changes > 0) {
            $empresa->sao = 1;
            $empresa->save();
        }

        return $changes;
    }

    /**
     * Ompli camps buits d'un centre de treball.
     *
     * @param Centro $centro
     * @param array<string, mixed> $data
     * @param bool $dryRun
     * @return int
     */
    public function fillMissingCentro(Centro $centro, array $data, bool $dryRun = false): int
    {
        $changes = $this->fillMissingFields(
            $centro,
            ['idSao', 'nombre', 'direccion', 'localidad', 'telefono', 'email', 'horarios', 'codiPostal'],
            $data
        );

        if (!$dryRun && $changes > 0) {
            $centro->save();
        }

        return $changes;
    }

    /**
     * Copia només valors SAO amb contingut sobre camps locals buits.
     *
     * @param object $model
     * @param array<int, string> $fields
     * @param array<string, mixed> $data
     * @return int
     */
    private function fillMissingFields(object $model, array $fields, array $data): int
    {
        $changes = 0;

        foreach ($fields as $field) {
            if (!$this->hasValue($data[$field] ?? null) || $this->hasValue($model->{$field} ?? null)) {
                continue;
            }

            $model->{$field} = $this->normalizeValue($data[$field]);
            $changes++;
        }

        return $changes;
    }

    private function hasValue(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        return true;
    }

    private function normalizeValue(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }
}
