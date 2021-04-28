<?php


namespace Intranet\Services;

use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;

class ColaboracionFindService
{
      protected $document;

    /**
     * DocFCTService constructor.
     * @param $tipo
     * @param $dni
     * @param $estado
     * @param $document
     * @param $id
     */
    public function __construct($document)
    {
        $this->document = $document;
    }

    public function exec(){
        $colaboraciones = Colaboracion::MiColaboracion(null,$this->document->dni)->where('tutor',$this->document->dni)->where($this->document->finder['params'][0],$this->document->finder['params'][1])->get();
        $this->markColaboracion($colaboraciones);
        return $colaboraciones;
    }


    private function markColaboracion(&$elements){
        foreach ($elements as $element){
            if (!$this->existsActivity($element->id) && $this->checkFcts($this->document->fcts,count($element->Fcts))){
                $element->marked = true;
            } else {
                $element->marked = false;
            }
        }
    }

    private function existsActivity($id){
        return Activity::where('model_class','Intranet\Entities\Colaboracion')->where('model_id',$id)->where('document','=',$this->document->subject)->count();
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