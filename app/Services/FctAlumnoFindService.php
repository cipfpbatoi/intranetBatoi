<?php


namespace Intranet\Services;


use Intranet\Entities\Activity;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;


class fctAlumnoFindService
{
      protected $dni;
      protected $document;



    /**
     * DocFCTService constructor.
     * @param $tipo
     * @param $dni
     * @param $estado
     * @param $document
     * @param $id
     */
    public function __construct( $dni, $document )
    {
        $this->dni = $dni;
        $this->document = $document;
    }

    public function exec(){
        $fcts = AlumnoFct::whereIn('idFct',hazArray(Fct::MisFctsColaboracion($this->dni)->EsFct()->get(),'id','id'))->get();
        $this->markFct($fcts);
        return $fcts;
    }

    private function markFct(&$elements){
        foreach ($elements as $element){
            if ($element->Fct->Nalumnes > 0 && $element->Fct->correoInstructor == 0 && Activity::where('model_class','Intranet\Entities\Fct')->where('model_id',$element->idFct)->where('document','=',$this->document['subject'])->count() == 0) {
                $element->marked = true;
            }
            else {
                $element->marked = false;
            }
        }
    }

}