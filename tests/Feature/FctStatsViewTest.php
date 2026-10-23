<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
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

        $html = $this->renderCycleName(7);

        $this->assertStringContainsString('CFGS Informàtica', $html);
    }

    public function test_no_falla_quan_el_cicle_no_existeix(): void
    {
        $html = $this->renderCycleName(99);

        $this->assertStringContainsString('Cicle no trobat (#99)', $html);
    }

    public function test_indica_quan_el_grup_no_te_cicle_assignat(): void
    {
        $html = $this->renderCycleName('');

        $this->assertStringContainsString('Sense cicle assignat', $html);
    }

    private function renderCycleName(int|string $id): string
    {
        return Blade::render('<x-fct.stats-cycle-name :id="$id" />', compact('id'));
    }
}
