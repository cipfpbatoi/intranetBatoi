<?php

namespace Tests\Unit\Entities;

use Illuminate\Support\Collection;
use Intranet\Entities\Tutoria;
use Tests\TestCase;

class TutoriaTest extends TestCase
{
    public function test_dates_i_mapes_config_son_null_safe(): void
    {
        config()->set('auxiliares.grupoTutoria', [1 => 'Tutor']);
        config()->set('auxiliares.tipoTutoria', [2 => 'Individual']);

        $tutoria = new Tutoria([
            'desde' => null,
            'hasta' => null,
            'grupos' => 99,
            'tipo' => 99,
        ]);

        $this->assertSame('', $tutoria->desde);
        $this->assertSame('', $tutoria->hasta);
        $this->assertSame('', $tutoria->grupo);
        $this->assertSame('', $tutoria->tipos);
    }

    public function test_feedback_sense_auth_retorna_quantitat_de_grups(): void
    {
        $tutoria = new Tutoria();
        $tutoria->setRelation('Grupos', new Collection([(object) ['codigo' => 'G1'], (object) ['codigo' => 'G2']]));

        $this->assertSame(2, $tutoria->feedBack);
    }
}

