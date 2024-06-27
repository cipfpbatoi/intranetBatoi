<?php

namespace Intranet\Finders;

use Intranet\Entities\AlumnoFct;


class AlumnoNoFctFinder extends Finder
{

    public function exec(){
        // Obtenir les FCTs vinculades a l'alumne, incloure el nom de l'alumne i ordenar per aquest nom
        $fcts = AlumnoFct::select('alumno_fcts.*', 'alumnos.nombre','alumnos.apellido1') // Assumeixo que 'nombre' és el camp del nom a la taula 'alumnos'
        ->join('alumnos', 'alumno_fcts.idAlumno', '=', 'alumnos.nia') // Uneix amb la taula 'alumnos' per a obtenir el nom
        ->where('alumno_fcts.hasta', '>', date('Y-m-d')) // Filtrar les FCTs que no han finalitzat (la data de finalització és superior a la data actual
        ->misFcts($this->dni) // Aquesta crida sembla ser específica del model i s'utilitza per a filtrar les FCTs
        ->orderBy('apellido1') // Ordenar els resultats pel nom de l'alumne
        ->orderBy('nombre') // Ordenar els resultats pel nom de l'alumne (en cas que hi hagi empats en el primer camp de l'ordre)
        ->get();

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
            // Marcar l'element segons la condició
            $element->marked = ($element->pg0301 == 0);
        }

        return $filteredElements;
    }


}