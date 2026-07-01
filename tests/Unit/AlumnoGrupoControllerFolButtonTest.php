<?php

declare(strict_types=1);

namespace Tests\Unit;

use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Grupo;
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

        $primer = $this->makeAlumnoGrupo('1', 0, 1);
        $segon = $this->makeAlumnoGrupo('2', 0, 2);

        $this->assertSame(['fol', '==', 0, 'curso', '==', 1], $where);
        $this->assertStringContainsString('http://intranet.test/alumno/1/checkFol', $boto->render($primer));
        $this->assertSame('', $boto->render($segon));

        $whereMarcat = $this->controller()->folWhere(1);
        $botoMarcat = new BotonImg('alumno.checkFol', [
            'img' => 'fa-check',
            'text' => 'Li correspon Certificat Fol',
            'where' => $whereMarcat,
        ]);

        $primerMarcat = $this->makeAlumnoGrupo('3', 1, 1);
        $segonMarcat = $this->makeAlumnoGrupo('4', 1, 2);

        $this->assertSame(['fol', '==', 1, 'curso', '==', 1], $whereMarcat);
        $this->assertStringContainsString('http://intranet.test/alumno/3/checkFol', $botoMarcat->render($primerMarcat));
        $this->assertSame('', $botoMarcat->render($segonMarcat));
    }

    public function test_periode_fol_no_amaga_botons_si_la_variable_no_esta_configurada(): void
    {
        config()->set('variables.certificatFol', null);
        $this->assertTrue($this->controller()->folPeriodIsOpen());

        config()->set('variables.certificatFol', date('Y-m-d', strtotime('+1 day')));
        $this->assertFalse($this->controller()->folPeriodIsOpen());
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

            /**
             * Exposa la finestra temporal FOL per provar configuració absent.
             */
            public function folPeriodIsOpen(): bool
            {
                return $this->folCertificatePeriodIsOpen();
            }
        };
    }

    private function makeAlumnoGrupo(string $nia, int $fol, int $curso): AlumnoGrupo
    {
        $alumne = new Alumno();
        $alumne->nia = $nia;
        $alumne->fol = $fol;

        $grup = new Grupo();
        $grup->codigo = 'G' . $curso;
        $grup->curso = $curso;

        $alumneGrup = new AlumnoGrupo();
        $alumneGrup->idAlumno = $nia;
        $alumneGrup->idGrupo = $grup->codigo;
        $alumneGrup->setRelation('Alumno', $alumne);
        $alumneGrup->setRelation('Grupo', $grup);

        return $alumneGrup;
    }
}
