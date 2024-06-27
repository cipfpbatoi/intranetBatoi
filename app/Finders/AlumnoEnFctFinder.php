<?php
namespace Intranet\Finders;

use Intranet\Entities\AlumnoFct;

class AlumnoEnFctFinder extends Finder
{
    public function exec()
    {
        $fcts = AlumnoFct::misFcts($this->dni)
            ->join('alumnos', 'alumno_fcts.idAlumno', '=', 'alumnos.nia')
            ->orderBy('alumnos.nombre')
            ->orderBy('alumnos.apellido1')
            ->orderBy('alumnos.apellido2')
            ->get(['alumno_fcts.*']); // Seleccionar tots els camps d'alumno_fcts

        return $this->filter($fcts);
    }

    private function filter(&$elements)
    {
        $uniqueAlumnos = [];
        $filteredElements = [];

        foreach ($elements as $element) {
            // Verificar si l'alumne ja ha estat afegit
            if (!isset($uniqueAlumnos[$element->idAlumno])) {
                // Afegir l'alumne a la llista de únics
                $uniqueAlumnos[$element->idAlumno] = true;
                $filteredElements[] = $element;
            }
            // Marcar l'element segons les dates proporcionades
            $element->marked = (fechaInglesa($element->desde) <= hoy() && fechaInglesa($element->hasta) >= hoy());
        }

        return $filteredElements;
    }
}
