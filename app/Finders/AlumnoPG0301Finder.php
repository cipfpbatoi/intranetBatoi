<?php

namespace Intranet\Finders;

use Intranet\Entities\AlumnoFct;


class AlumnoPG0301Finder extends Finder
{
    public function exec(){
        $fcts =  AlumnoFct::misFcts()->where('pg0301',0)->get();
        return $this->filter($fcts);
    }

    private function filter(&$elements){
        foreach ($elements as $element){
            $element->marked = ($element->pg0301 == 0);
        }
        return $elements;
    }

}