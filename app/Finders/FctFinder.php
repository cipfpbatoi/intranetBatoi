<?php
namespace Intranet\Finders;

use Intranet\Entities\Fct;
use Intranet\Services\UI\AppAlert as Alert;

class FctFinder extends Finder
{
    public function exec()
    {
        $fcts = Fct::MisFcts($this->dni)
            ->orWhere('cotutor', $this->dni)
            ->has('AlFct')
            //->EsFct()
            //->where('correoInstructor', 0)
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
                $element->marked = false;
            } else {
                $element->marked = !$this->existsActivity($element->id)?true:false;
            }
        }
        return $elements;
    }
}
