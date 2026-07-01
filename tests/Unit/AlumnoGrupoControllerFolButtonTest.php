<?php

declare(strict_types=1);

namespace Tests\Unit;

use Intranet\Http\Controllers\AlumnoGrupoController;
use Intranet\UI\Botones\BotonImg;
use Tests\TestCase;

/**
 * Proves de la visibilitat del botó individual del certificat FOL en alumnat de grup.
 */
class AlumnoGrupoControllerFolButtonTest extends TestCase
{
    public function test_boto_de_certificat_fol_nomes_renderitza_en_alumnat_de_primer(): void
    {
        config()->set('app.url', 'http://intranet.test');

        $where = $this->controller()->folWhere(0);
        $boto = new BotonImg('alumno.checkFol', [
            'img' => 'fa-square-o',
            'text' => 'Li correspon Certificat Fol',
            'where' => $where,
        ]);

        $primer = $this->makeElement(['id' => 1, 'fol' => 0, 'curso' => 1]);
        $segon = $this->makeElement(['id' => 2, 'fol' => 0, 'curso' => 2]);

        $this->assertSame(['fol', '==', 0, 'curso', '==', 1], $where);
        $this->assertStringContainsString('http://intranet.test/alumno/1/checkFol', $boto->render($primer));
        $this->assertSame('', $boto->render($segon));

        $whereMarcat = $this->controller()->folWhere(1);
        $botoMarcat = new BotonImg('alumno.checkFol', [
            'img' => 'fa-check',
            'text' => 'Li correspon Certificat Fol',
            'where' => $whereMarcat,
        ]);

        $primerMarcat = $this->makeElement(['id' => 3, 'fol' => 1, 'curso' => 1]);
        $segonMarcat = $this->makeElement(['id' => 4, 'fol' => 1, 'curso' => 2]);

        $this->assertSame(['fol', '==', 1, 'curso', '==', 1], $whereMarcat);
        $this->assertStringContainsString('http://intranet.test/alumno/3/checkFol', $botoMarcat->render($primerMarcat));
        $this->assertSame('', $botoMarcat->render($segonMarcat));
    }

    private function controller(): object
    {
        return new class extends AlumnoGrupoController {
            /**
             * Exposa la condició del botó FOL per a provar-la sense renderitzar el panell complet.
             *
             * @param int $fol Estat actual del certificat FOL de l'alumne.
             * @return array<int, int|string>
             */
            public function folWhere(int $fol): array
            {
                return $this->folCertificateButtonWhere($fol);
            }
        };
    }

    private function makeElement(array $values): object
    {
        return new class($values) {
            public function __construct(private array $values)
            {
                foreach ($values as $key => $value) {
                    $this->$key = $value;
                }
            }

            public function getKey(): int|string|null
            {
                return $this->values['id'] ?? null;
            }
        };
    }
}
