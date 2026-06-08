<?php

namespace Tests\Unit\Controllers;

use Illuminate\Support\Collection;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Intranet\Entities\Grupo;
use Intranet\Http\Controllers\PanelFctAvalController;
use ReflectionClass;
use Tests\TestCase;

/**
 * Proves unitàries de classificació d'alumnat FCT en el panell d'avaluació.
 */
class PanelFctAvalControllerTest extends TestCase
{
    public function test_convalidat_lfp_sense_colaboracio_agafa_normativa_del_grup_de_la_persona(): void
    {
        $controller = (new ReflectionClass(PanelFctAvalController::class))->newInstanceWithoutConstructor();
        $fct = $this->makeAlumnoFctWithoutCicle('LFP');

        $grupo = $this->invokePrivate($controller, 'resolveGrupoForFct', [$fct]);
        $normativa = $this->invokePrivate($controller, 'resolveNormativa', [$fct, $grupo]);

        $this->assertInstanceOf(Grupo::class, $grupo);
        $this->assertSame('LFP', $normativa);
    }

    public function test_convalidat_sense_colaboracio_prioritza_el_grup_de_curs_mes_alt_de_la_persona(): void
    {
        $controller = (new ReflectionClass(PanelFctAvalController::class))->newInstanceWithoutConstructor();
        $fct = $this->makeAlumnoFctWithGroups([
            $this->makeGrupo('G1LOE', 'Primer LOE', 1, 'LOE'),
            $this->makeGrupo('G2LFP', 'Segon LFP', 2, 'LFP'),
        ]);

        $grupo = $this->invokePrivate($controller, 'resolveGrupoForFct', [$fct]);

        $this->assertSame('G2LFP', $grupo?->codigo);
    }

    private function makeAlumnoFctWithoutCicle(string $normativa): AlumnoFct
    {
        return $this->makeAlumnoFctWithGroups([
            $this->makeGrupo('G2' . $normativa, 'Segon (' . $normativa . ')', 2, $normativa),
        ]);
    }

    /**
     * Crea una FCT d'alumne sense cicle en la col·laboració.
     *
     * @param array<int, Grupo> $grupos
     * @return AlumnoFct
     */
    private function makeAlumnoFctWithGroups(array $grupos): AlumnoFct
    {
        $alumno = new Alumno();
        $alumno->setRelation('Grupo', new Collection($grupos));

        $fct = new Fct();
        $fct->asociacion = 2;
        $fct->setRelation('Colaboracion', null);

        $alumnoFct = new AlumnoFct();
        $alumnoFct->setRelation('Alumno', $alumno);
        $alumnoFct->setRelation('Fct', $fct);

        return $alumnoFct;
    }

    private function makeGrupo(string $codigo, string $nombre, int $curso, string $normativa): Grupo
    {
        $grupo = new Grupo();
        $grupo->codigo = $codigo;
        $grupo->nombre = $nombre;
        $grupo->curso = $curso;
        $grupo->setRelation('Ciclo', (object) ['normativa' => $normativa, 'tipo' => 2]);

        return $grupo;
    }

    /**
     * Invoca un mètode privat del controlador sota prova.
     *
     * @param object $object
     * @param string $method
     * @param array<int, mixed> $arguments
     * @return mixed
     */
    private function invokePrivate(object $object, string $method, array $arguments = []): mixed
    {
        $reflection = new ReflectionClass($object);
        $methodReflection = $reflection->getMethod($method);
        $methodReflection->setAccessible(true);

        return $methodReflection->invokeArgs($object, $arguments);
    }
}
