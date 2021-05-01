<?php
namespace Intranet\Finders;

use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;

class ColaboracionFinder extends Finder
{

    protected $modelo = "Intranet\\Entities\\Colaboracion";

    public function exec(){
        $colaboraciones = Colaboracion::MiColaboracion(null,$this->dni)->where('tutor',$this->dni)->where('estado',$this->document->estado)->get();
        return $this->filter($colaboraciones);
    }

    public function filter(&$elements){
        foreach ($elements as $element){
            $element->marked = (!$this->existsActivity($element->id) && $this->checkFcts($this->document->fcts,count($element->Fcts)))?true:false;
        }
        return $elements;
    }

    private function checkFcts($needsFcts,$existsFcts){
        if ($needsFcts && $existsFcts) {
            return true;
        }
        if (!$needsFcts && !$existsFcts) {
            return true;
        }
        return false;
    }


}