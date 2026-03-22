<?php

namespace Tests\Unit\Entities;

use Intranet\Entities\Grupo;
use Intranet\Entities\TutoriaGrupo;
use Tests\TestCase;

class TutoriaGrupoTest extends TestCase
{
    public function test_fecha_i_nombre_accessors_son_null_safe(): void
    {
        $tg = new TutoriaGrupo([
            'fecha' => null,
        ]);
        $tg->setRelation('Grupo', null);

        $this->assertSame('', $tg->fecha);
        $this->assertSame('', $tg->nombre);
    }

    public function test_nombre_accessor_torna_nom_quan_hi_ha_relacio(): void
    {
        $tg = new TutoriaGrupo();
        $tg->setRelation('Grupo', new Grupo(['nombre' => '1SMX']));

        $this->assertSame('1SMX', $tg->nombre);
    }
}

