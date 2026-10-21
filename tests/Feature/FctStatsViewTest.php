<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Proves de regressió de la vista d'estadístiques FCT.
 */
class FctStatsViewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->create('ciclos', function (Blueprint $table): void {
            $table->id();
            $table->string('ciclo');
        });
    }

    public function test_mostra_el_nom_quan_el_cicle_existeix(): void
    {
        DB::table('ciclos')->insert(['id' => 7, 'ciclo' => 'CFGS Informàtica']);

        $html = $this->renderStats(['7' => $this->resultatCicle()]);

        $this->assertStringContainsString('CFGS Informàtica', $html);
    }

    public function test_no_falla_quan_el_cicle_no_existeix(): void
    {
        $html = $this->renderStats(['99' => $this->resultatCicle()]);

        $this->assertStringContainsString('Cicle no trobat (#99)', $html);
    }

    public function test_indica_quan_el_grup_no_te_cicle_assignat(): void
    {
        $html = $this->renderStats(['' => $this->resultatCicle()]);

        $this->assertStringContainsString('Sense cicle assignat', $html);
    }

    /**
     * @param array<string, array<string, int>> $ciclos
     */
    private function renderStats(array $ciclos): string
    {
        return view('fct.estadisticas', [
            'grupos1' => collect(),
            'grupos2' => collect(),
            'ciclos1' => $ciclos,
            'ciclos2' => [],
        ])->render();
    }

    /**
     * @return array<string, int>
     */
    private function resultatCicle(): array
    {
        return [
            'matriculados' => 1,
            'resfct' => 1,
            'avalfct' => 1,
            'exentos' => 0,
            'respro' => 0,
            'avalpro' => 0,
            'resempresa' => 0,
        ];
    }
}
