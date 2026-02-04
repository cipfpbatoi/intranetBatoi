<?php

declare(strict_types=1);

namespace Tests\Unit\Botones;

use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Botones\Panel;
use Tests\TestCase;

class PanelTest extends TestCase
{
    public function test_constructor_inicialitza_pestana_i_botons_buits(): void
    {
        $panel = new Panel('Profesor', ['nom', 'email']);

        $this->assertSame('Profesor', $panel->getModel());
        $this->assertCount(1, $panel->getPestanas());
        $this->assertSame('grid', $panel->getPestanas()[0]->getNombre());
        $this->assertSame('active', $panel->getPestanas()[0]->getActiva());
        $this->assertSame([], $panel->getBotones('index'));
        $this->assertSame([], $panel->getBotones('grid'));
        $this->assertSame([], $panel->getBotones('profile'));
    }

    public function test_set_botonera_crea_tipus_correctes(): void
    {
        $panel = new Panel('profesor', null, null, false);
        $panel->setPestana('grid', true);

        $panel->setBotonera(['create'], ['edit'], ['show']);

        $this->assertInstanceOf(BotonBasico::class, $panel->getBotones('index')[0]);
        $this->assertInstanceOf(BotonImg::class, $panel->getBotones('grid')[0]);
        $this->assertInstanceOf(BotonIcon::class, $panel->getBotones('profile')[0]);
    }

    public function test_set_both_boton_afegeix_grid_i_profile(): void
    {
        $panel = new Panel('profesor', null, null, false);
        $panel->setPestana('grid', true);

        $panel->setBothBoton('profesor.edit', ['img' => 'fa-edit']);

        $this->assertCount(1, $panel->getBotones('grid'));
        $this->assertCount(1, $panel->getBotones('profile'));
        $this->assertInstanceOf(BotonImg::class, $panel->getBotones('grid')[0]);
        $this->assertInstanceOf(BotonIcon::class, $panel->getBotones('profile')[0]);
    }

    public function test_activa_pestana_desactiva_la_resta(): void
    {
        $panel = new Panel('profesor');
        $panel->setPestana('segona', false);

        $panel->activaPestana('segona');

        $this->assertSame('', $panel->getPestanas()[0]->getActiva());
        $this->assertSame('active', $panel->getPestanas()[1]->getActiva());
    }

    public function test_get_elementos_aplica_filtro_de_pestana(): void
    {
        $panel = new Panel('profesor', null, null, false);
        $panel->setPestana('filtrada', true, null, ['estado', 1]);
        $panel->setElementos(collect([
            (object) ['id' => 1, 'estado' => 1, 'nom' => 'Primer'],
            (object) ['id' => 2, 'estado' => 0, 'nom' => 'Segon'],
        ]));

        $resultat = $panel->getElementos($panel->getPestanas()[0]);

        $this->assertCount(1, $resultat);
        $this->assertSame('Primer', $resultat->first()->nom);
    }

    public function test_get_last_pestana_with_modals_torna_els_modals_de_l_ultima(): void
    {
        $panel = new Panel('profesor');
        $panel->setPestana('extra', false, null, null, null, null, [
            'modal' => ['extended', 'signatura'],
        ]);

        $this->assertSame(['extended', 'signatura'], $panel->getLastPestanaWithModals());
    }
}
