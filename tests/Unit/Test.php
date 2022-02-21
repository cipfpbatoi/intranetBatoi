<?php

namespace Tests\Unit;

use Intranet\Botones\BotonBasico;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{

    /** @test */
    function testBotonBasicoHtml(){
        $boton = new BotonBasico('ciclo.create');

        $this->assertEquals("<a href='/ciclo/create'></a>",$boton->show());
    }
}
