<?php

namespace Intranet\Finders;

use Intranet\Entities\AlumnoFct;


class AlumnoNoFctFinder extends Finder
{
    public function exec(){
        $fcts =  AlumnoFct::misFcts($this->dni)->orderBy('idFct')->orderBy('desde')->get();
        return $this->filter($fcts);
    }

    private function filter(&$elements){
        foreach ($elements as $element){
            $element->marked = ($element->pg0301 == 0);
        }
        return $elements;
    }

}