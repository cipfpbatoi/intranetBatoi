<?php

declare(strict_types=1);

namespace Tests\Unit;

use Intranet\Entities\Grupo;
use Intranet\Http\Controllers\GrupoController;
use Intranet\UI\Botones\BotonImg;
use Tests\TestCase;

/**
 * Proves de la visibilitat del botó de comprovació FOL en la graella de grups.
 */
class GrupoControllerFolButtonTest extends TestCase
{
    public function test_boto_de_certificat_fol_de_grup_nomes_renderitza_en_grups_de_primer(): void
    {
        config()->set('app.url', 'http://intranet.test');

        $where = $this->controller()->folWhere(0);
        $boto = new BotonImg('grupo.fol', [
            'img' => 'fa-square-o',
            'text' => 'Comprovat a quin alumnat li correspon Certificat fol',
            'where' => $where,
        ]);

        $primer = $this->makeGrupo('1CF', 0, 1);
        $segon = $this->makeGrupo('2CF', 0, 2);

        $this->assertSame(['fol', '==', 0, 'curso', '==', 1], $where);
        $this->assertStringContainsString('http://intranet.test/grupo/1CF/fol', $boto->render($primer));
        $this->assertSame('', $boto->render($segon));

        $whereMarcat = $this->controller()->folWhere(1);
        $botoMarcat = new BotonImg('grupo.fol', [
            'img' => 'fa-check',
            'text' => 'Comprovat a quin alumnat li correspon Certificat fol',
            'where' => $whereMarcat,
        ]);

        $primerMarcat = $this->makeGrupo('1DAW', 1, 1);
        $segonMarcat = $this->makeGrupo('2DAW', 1, 2);

        $this->assertSame(['fol', '==', 1, 'curso', '==', 1], $whereMarcat);
        $this->assertStringContainsString('http://intranet.test/grupo/1DAW/fol', $botoMarcat->render($primerMarcat));
        $this->assertSame('', $botoMarcat->render($segonMarcat));
    }

    private function controller(): object
    {
        return new class extends GrupoController {
            /**
             * Exposa la condició del botó FOL per a provar-la sense renderitzar el panell complet.
             *
             * @param int $fol Estat actual de comprovació FOL del grup.
             * @return array<int, int|string>
             */
            public function folWhere(int $fol): array
            {
                return $this->folCertificateButtonWhere($fol);
            }
        };
    }

    private function makeGrupo(string $codigo, int $fol, int $curso): Grupo
    {
        $grupo = new Grupo();
        $grupo->codigo = $codigo;
        $grupo->fol = $fol;
        $grupo->curso = $curso;

        return $grupo;
    }
}
