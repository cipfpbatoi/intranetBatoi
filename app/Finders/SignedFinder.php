<?php
namespace Intranet\Finders;

use Illuminate\Database\Eloquent\Collection;
use Intranet\Entities\AlumnoFct;

class SignedFinder extends Finder
{
    public function exec()
    {
        $fcts =  AlumnoFct::misFcts($this->dni)
            ->orderBy('idAlumno')
            ->orderBy('desde')
            ->get();
        return $this->filter($fcts);
    }

    private function filter(&$aluFcts)
    {
        $elements = new Collection();

        foreach ($aluFcts as $element) {
            if ($element->signatures->count() > 0) {
                $correcte = true;
                foreach ($element->signatures as $signature) {
                    if ($signature->tipus == 'A3' && $signature->signed < 2) {
                        $correcte = false;
                    }
                    if ($signature->tipus == 'A2' && $signature->signed < 2) {
                        $correcte = false;
                    }
                    if ($signature->tipus == 'A1' && $signature->signed < 1) {
                        $correcte = false;
                    }
                }
                if ($correcte) {
                    $element->marked = true;
                } else {
                    $element->marked = false;
                }
                $elements->push($element);
            }
        }
        return $elements;
    }
}
