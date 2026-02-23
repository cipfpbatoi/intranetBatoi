<?php

namespace Tests\Unit\Services;

use Intranet\Entities\Signatura;
use Intranet\Services\School\SignaturaStatusService;
use Tests\TestCase;

class SignaturaStatusServiceTest extends TestCase
{
    public function test_estat_i_css_class(): void
    {
        $service = new SignaturaStatusService();

        $a1 = new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 3]);
        $this->assertSame('Signatura Direcció completada', $service->estat($a1));

        $a3 = new Signatura(['tipus' => 'A3', 'sendTo' => 2, 'signed' => 2]);
        $this->assertSame("Enviat a l'instructor sense la signatura de l'alumne", $service->estat($a3));

        $unknown = new Signatura(['tipus' => 'ZX', 'sendTo' => 0, 'signed' => 0]);
        $this->assertSame('Tipus desconegut', $service->estat($unknown));

        $this->assertSame('bg-orange', $service->cssClass(new Signatura(['tipus' => 'A3', 'sendTo' => 1, 'signed' => 2])));
        $this->assertSame('bg-green', $service->cssClass(new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 3])));
        $this->assertSame('bg-blue-sky', $service->cssClass(new Signatura(['tipus' => 'A1', 'sendTo' => 1, 'signed' => 3])));
        $this->assertSame('bg-red', $service->cssClass(new Signatura(['tipus' => 'A1', 'sendTo' => 0, 'signed' => 1])));
    }

    public function test_yes_no(): void
    {
        $service = new SignaturaStatusService();

        $this->assertSame('Sí', $service->yesNo(true));
        $this->assertSame('Sí', $service->yesNo(1));
        $this->assertSame('No', $service->yesNo(false));
        $this->assertSame('No', $service->yesNo(0));
    }
}

