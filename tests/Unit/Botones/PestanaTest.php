<?php

declare(strict_types=1);

namespace Tests\Unit\Botones;

use Intranet\UI\Panels\Pestana;
use Tests\TestCase;

class PestanaTest extends TestCase
{
    public function test_constructor_i_getters_basics(): void
    {
        $pestana = new Pestana(
            'grid',
            true,
            'intranet.partials.grid.profesor',
            ['estado', 1],
            ['dni', 'nom'],
            ['modal' => ['extended']]
        );

        $this->assertSame('grid', $pestana->getNombre());
        $this->assertSame('active', $pestana->getActiva());
        $this->assertSame('intranet.partials.grid.profesor', $pestana->getVista());
        $this->assertSame(['estado', 1], $pestana->getFiltro());
        $this->assertSame(['dni', 'nom'], $pestana->getRejilla());
        $this->assertSame(['extended'], $pestana->getInclude('modal'));
    }

    public function test_setters_actualitzen_valors(): void
    {
        $pestana = new Pestana('index');

        $pestana->setActiva(true);
        $pestana->setVista('intranet.partials.index.profesor');
        $pestana->setRejilla(['id']);
        $pestana->setInclude(['modal' => ['signatura']]);

        $this->assertSame('active', $pestana->getActiva());
        $this->assertSame('intranet.partials.index.profesor', $pestana->getVista());
        $this->assertSame(['id'], $pestana->getRejilla());
        $this->assertSame(['signatura'], $pestana->getInclude('modal'));
    }

    public function test_get_filtro_torna_array_buit_quan_es_null(): void
    {
        $pestana = new Pestana('sense-filtro', false, null, null);

        $this->assertSame([], $pestana->getFiltro());
    }

    public function test_get_label_fa_fallback_a_nom_quan_no_hi_ha_traduccio(): void
    {
        $pestana = new Pestana('clau_que_no_existeix');

        $this->assertSame('clau_que_no_existeix', $pestana->getLabel());
    }
}
