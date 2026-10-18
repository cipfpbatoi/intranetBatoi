<?php

declare(strict_types=1);

namespace Intranet\Presentation\Colaboracion;

use Illuminate\Support\Str;

/**
 * Normalitza noms lliures de municipi per a agrupar-los en filtres de presentació.
 */
final class MunicipalityNameNormalizer
{
    /**
     * Índex d'àlies calculat a partir de la configuració.
     *
     * @var array<string, array{key:string,label:string}>|null
     */
    private ?array $aliasIndex = null;

    /**
     * Retorna la clau de filtre i l'etiqueta visible d'un municipi.
     *
     * @return array{key:string,label:string}
     */
    public function normalize(?string $municipality): array
    {
        $label = preg_replace('/\s+/u', ' ', trim((string) $municipality)) ?: '';

        if ($label === '') {
            return ['key' => 'desconeguda', 'label' => 'Desconeguda'];
        }

        $comparisonKey = $this->comparisonKey($label);

        return $this->aliasIndex()[$comparisonKey] ?? [
            'key' => Str::lower(str_replace(' ', '-', $comparisonKey)),
            'label' => $label,
        ];
    }

    /**
     * Construeix l'índex dels àlies configurats amb la seua etiqueta valenciana.
     *
     * @return array<string, array{key:string,label:string}>
     */
    private function aliasIndex(): array
    {
        if ($this->aliasIndex !== null) {
            return $this->aliasIndex;
        }

        $this->aliasIndex = [];

        foreach ((array) config('municipalities.aliases', []) as $canonical => $aliases) {
            $canonicalLabel = trim((string) $canonical);
            $canonicalKey = Str::lower(str_replace(' ', '-', $this->comparisonKey($canonicalLabel)));
            $municipality = ['key' => $canonicalKey, 'label' => $canonicalLabel];

            foreach (array_merge([$canonicalLabel], (array) $aliases) as $alias) {
                $this->aliasIndex[$this->comparisonKey((string) $alias)] = $municipality;
            }
        }

        return $this->aliasIndex;
    }

    /**
     * Genera una forma comparable ignorant accents, caixa i puntuació.
     */
    private function comparisonKey(string $municipality): string
    {
        $ascii = mb_strtoupper(Str::ascii($municipality), 'UTF-8');

        return trim((string) preg_replace('/[^A-Z0-9]+/', ' ', $ascii));
    }
}
