<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Proves de la secció de promoció en l'acta extraordinària.
 */
class ReunionActaAvaluacioViewTest extends TestCase
{
    public function test_acta_extraordinaria_mostra_promocio_i_no_promocio(): void
    {
        $html = view('pdf.reunion.partials.promocio', [
            'datosInforme' => new FakeActaExtraordinaria([
                $this->alumne('Aitana Pla Soler', 1),
                $this->alumne('Bernat Ribes Gil', 3),
            ]),
        ])->render();

        $this->assertStringContainsString('Promoció de l\'alumnat', $html);
        $this->assertStringContainsString('Aitana Pla Soler - SI', $html);
        $this->assertStringContainsString('Promociona', $html);
        $this->assertStringContainsString('Bernat Ribes Gil - NO', $html);
        $this->assertStringContainsString('No Promociona', $html);
    }

    public function test_acta_extraordinaria_no_falla_amb_capacitats_no_previstes(): void
    {
        $html = view('pdf.reunion.partials.promocio', [
            'datosInforme' => new FakeActaExtraordinaria([
                $this->alumne('Carme Vidal Mas', 0),
            ]),
        ])->render();

        $this->assertStringContainsString('Carme Vidal Mas - Sense determinar', $html);
    }

    private function alumne(string $nameFull, int $capacitats): object
    {
        return (object) [
            'nameFull' => $nameFull,
            'pivot' => (object) ['capacitats' => $capacitats],
        ];
    }
}

/**
 * Doble mínim de l'acta amb alumnat ordenable per a renderitzar la vista.
 */
class FakeActaExtraordinaria
{
    public function __construct(private readonly array $alumnes)
    {
    }

    public function alumnos(): FakeActaAlumnesQuery
    {
        return new FakeActaAlumnesQuery($this->alumnes);
    }
}

/**
 * Doble mínim de query Eloquent utilitzat per la plantilla.
 */
class FakeActaAlumnesQuery
{
    public function __construct(private readonly array $alumnes)
    {
    }

    public function orderBy(string $column): self
    {
        return $this;
    }

    public function get(): Collection
    {
        return collect($this->alumnes);
    }
}
