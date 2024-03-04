<?php
namespace Intranet\Finders;

use Intranet\Entities\Fct;
use Styde\Html\Facades\Alert;

class FctFinder extends Finder
{
    public function exec()
    {
        $fcts = Fct::MisFcts($this->dni)->orWhere('cotutor', $this->dni)->EsFct()->get();
        return $this->filter($fcts);
    }

    private function filter(&$elements)
    {

        foreach ($elements as $element) {
            $fechaUltimaFct = isset($element->AlFct()->first()->hasta) ?
                $element->AlFct()->first()->hasta :
                null;
            if ($fechaUltimaFct && fechaInglesa($fechaUltimaFct) < hoy()) {
                $element->marked = false;
            } else {
                $element->marked = !$this->existsActivity($element->id)?true:false;
            }
        }
        return $elements;
    }
}
