<?php
namespace Intranet\Finders;

use Intranet\Entities\Fct;
use Styde\Html\Facades\Alert;

class FctFinder extends Finder
{
    public function exec(){
        $fcts = Fct::MisFctsColaboracion($this->dni)->EsFct()->get();
        return $this->filter($fcts);
    }

    private function filter(&$elements){
        foreach ($elements as $element){
            $fechaUltimaFct = $element->AlFct()->orderBy('hasta','desc')->first()->hasta;
            if (FechaInglesa($fechaUltimaFct) < Hoy()){
                $element->marked = false;
            } else {
                $element->marked = !$this->existsActivity($element->id)?true:false;
            }
        }
        return $elements;
    }
}