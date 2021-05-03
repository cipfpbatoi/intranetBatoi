<?php
namespace Intranet\Finders;

use Intranet\Entities\Fct;

class FctFinder extends Finder
{
    public function exec(){
        $fcts = Fct::MisFctsColaboracion($this->dni)->EsFct()->get();
        return $this->filter($fcts);
    }

    private function filter(&$elements){
        foreach ($elements as $element){
            $element->marked = !$this->existsActivity($element->id)?true:false;
        }
        return $elements;
    }
}