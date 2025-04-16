<?php
namespace Intranet\Finders;

use Illuminate\Support\Facades\Log;
use Intranet\Entities\Alumno;

class AlumnoFctNoFinder extends Finder
{

    public function exec()
    {
        // Obtenir tots els alumnes del grup
        $alumnes = Alumno::MisAlumnos($this->dni)
            ->select('alumnos.nia', 'alumnos.nombre', 'alumnos.apellido1')
            ->leftJoin('alumno_fcts', 'alumnos.nia', '=', 'alumno_fcts.idAlumno')
            ->selectRaw('IFNULL(COUNT(alumno_fcts.id), 0) as fct_count') // Comptar les FCTs
            ->selectRaw('SUM(CASE WHEN alumno_fcts.calificacion IS NULL THEN 1 ELSE 0 END) as fct_pendents') // Comptar les FCTs pendents
            ->groupBy('alumnos.nia', 'alumnos.nombre', 'alumnos.apellido1')
            ->orderBy('apellido1')
            ->orderBy('nombre')
            ->get();
        Log::info($alumnes);
        return $this->filter($alumnes);
    }

// Marcar els alumnes que NO tenen FCT o NO han començat cap FCT
    private function filter(&$elements)
    {
        foreach ($elements as $element) {
            $element->marked = ($element->fct_count == 0 || $element->fct_pendents > 0);
        }
        return $elements;
    }



}