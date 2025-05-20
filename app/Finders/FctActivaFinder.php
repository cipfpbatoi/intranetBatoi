<?php
namespace Intranet\Finders;

use Intranet\Entities\Fct;
use Styde\Html\Facades\Alert;

class FctActivaFinder extends Finder
{
    public function exec()
    {
        $fcts = Fct::MisFcts($this->dni)
            ->orWhere('cotutor', $this->dni)
            ->has('AlFct')
            ->EsFct()
            ->join('instructores', 'fcts.idInstructor', '=', 'instructores.dni') // Assuming the relationship field names
            ->orderBy('instructores.name')
            ->orderBy('instructores.surnames')
            ->get(['fcts.*']); // Ensure you only select the fcts columns to avoid conflict
        return $this->filter($fcts);
    }

    private function filter(&$elements)
    {

        foreach ($elements as $element) {
            $fechaUltimaFct = isset($element->AlFct()->first()->hasta) ?
                $element->AlFct()->first()->hasta :
                null;
            if ($fechaUltimaFct && fechaInglesa($fechaUltimaFct) > hoy()) {
                $element->marked = true;
            } else {
                $element->marked =  false;
            }
        }
        return $elements;
    }
}
