<?php


namespace Intranet\Finders;

use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;


class AlumnoFctFinder extends Finder
{
    public function exec(){
        $fcts = AlumnoFct::whereIn('idFct',hazArray(Fct::MisFctsColaboracion($this->dni)->EsFct()->get(),'id','id'))->get();
        return $this->filter($fcts);
    }

    private function filter(&$elements){
        foreach ($elements as $element){
            $element->marked = ($element->Fct->correoInstructor == 0 && !$this->existsActivity($element->idFct))?true:false;
        }
        return $elements;
    }

}