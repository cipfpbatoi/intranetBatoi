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
            ->orderBy('alumno_fcts.desde')
            ->get(['alumno_fcts.*']); // Seleccionar tots els camps d'alumno_fcts

        return $this->filter($fcts);
    }

    private function filter(&$elements)
    {
        foreach ($elements as $element) {
            // Verificar si l'alumne ja ha estat afegit

            // Marcar l'element segons les dates proporcionades
            $element->marked = (fechaInglesa($element->desde) <= hoy() && fechaInglesa($element->hasta) >= hoy());
        }

        return $elements;
    }
}
