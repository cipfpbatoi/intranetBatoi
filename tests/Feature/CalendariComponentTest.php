<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Intranet\Entities\CalendariEscolar;
use Intranet\Livewire\CalendariComponent;
use Livewire\Livewire;
use Tests\TestCase;

class CalendariComponentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function genera_graella_de_42_dies_comencant_dilluns()
    {
        // Març 2024 comença en divendres; la graella ha d'incloure dies previs del febrer
        $component = Livewire::test(CalendariComponent::class, ['any' => 2024, 'mes' => 3]);

        $grid = $component->get('grid');
        $this->assertCount(42, $grid);

        $primer = $grid[0];
        $this->assertSame('2024-02-26', $primer['data']); // Dilluns anterior al primer de mes
        $this->assertFalse($primer['es_mes_actual']);
    }

    /** @test */
    public function guarda_un_dia_amb_data_completa_i_l_actualitza_al_calendari()
    {
        $component = Livewire::test(CalendariComponent::class, ['any' => 2025, 'mes' => 5]);

        $component->call('seleccionarDia', 10);
        $component->set('tipus', 'festiu');
        $component->set('esdeveniment', 'Prova esdeveniment');
        $component->call('guardarCanvis');

        $this->assertDatabaseHas(CalendariEscolar::class, [
            'data' => Carbon::create(2025, 5, 10)->toDateString(),
            'tipus' => 'festiu',
            'esdeveniment' => 'Prova esdeveniment',
        ]);

        $esdeveniments = $component->get('esdeveniments');
        $this->assertContains(['dia' => 10, 'esdeveniment' => 'Prova esdeveniment'], $esdeveniments);
    }
}
