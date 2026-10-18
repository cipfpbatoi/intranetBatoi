<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Colaboracion;

use Intranet\Presentation\Colaboracion\MunicipalityNameNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Proves de normalització dels municipis del filtre de col·laboracions.
 */
class MunicipalityNameNormalizerTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function muroAliases(): array
    {
        return [
            'nom curt' => ['Muro'],
            'castellà' => ['Muro de Alcoy'],
            'valencià' => ["Muro d'Alcoi"],
            'valencià amb apòstrof tipogràfic' => ['MURO D’ALCOI'],
        ];
    }

    /**
     * @return array<string, array{string,string,string}>
     */
    public static function canonicalAliases(): array
    {
        return [
            'Alcoi en castellà' => ['Alcoy', 'alcoi', 'Alcoi'],
            'Alcoi en majúscules' => ['ALCOI', 'alcoi', 'Alcoi'],
            'València sense accent' => ['Valencia', 'valencia', 'València'],
            'Xàtiva sense accent' => ['Xativa', 'xativa', 'Xàtiva'],
            'Xàtiva en castellà' => ['Játiva', 'xativa', 'Xàtiva'],
            'Dénia sense accent' => ['Denia', 'denia', 'Dénia'],
            'Ontinyent en castellà' => ['Onteniente', 'ontinyent', 'Ontinyent'],
        ];
    }

    /** Cada variant coneguda de Muro compartix una única clau i etiqueta. */
    #[DataProvider('muroAliases')]
    public function test_unifica_els_alias_de_muro(string $municipality): void
    {
        $normalized = (new MunicipalityNameNormalizer())->normalize($municipality);

        $this->assertSame('muro-d-alcoi', $normalized['key']);
        $this->assertSame('Muro d’Alcoi', $normalized['label']);
    }

    /** Els àlies configurats compartixen la clau i el nom valencià canònic. */
    #[DataProvider('canonicalAliases')]
    public function test_normalitza_els_alias_de_municipis(
        string $municipality,
        string $expectedKey,
        string $expectedLabel
    ): void {
        $normalized = (new MunicipalityNameNormalizer())->normalize($municipality);

        $this->assertSame($expectedKey, $normalized['key']);
        $this->assertSame($expectedLabel, $normalized['label']);
    }

    /** La normalització general ignora caixa, accents i puntuació en la clau. */
    public function test_genera_la_mateixa_clau_general_sense_alterar_les_dades(): void
    {
        $normalizer = new MunicipalityNameNormalizer();

        $this->assertSame(
            $normalizer->normalize('L’Alcúdia')['key'],
            $normalizer->normalize("l'alcudia")['key']
        );
        $this->assertSame('L’Alcúdia', $normalizer->normalize('L’Alcúdia')['label']);
    }
}
