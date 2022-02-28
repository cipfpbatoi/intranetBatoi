<?php

namespace Intranet\Finders;

use Intranet\Entities\AlumnoFct;


class AlumnoEnFctFinder extends Finder
{
    public function exec(){

        $fcts =  AlumnoFct::misFcts($this->dni)->orderBy('idAlumno')->orderBy('desde')->get();
        return $this->filter($fcts);
    }

    private function filter(&$elements){
        foreach ($elements as $element){
            $element->marked = (FechaInglesa($element->desde) <= Hoy() && FechaInglesa($element->hasta) >= Hoy());
        }
        return $elements;

    }
}