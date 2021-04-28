<?php


namespace Intranet\Services;


use Intranet\Entities\Activity;
use Intranet\Entities\Fct;


class fctFindService
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
        $fcts = Fct::MisFctsColaboracion($this->dni)->EsFct()->get();
        $this->markFct($fcts);
        return $fcts;
    }

    private function markFct(&$elements){
        foreach ($elements as $element){
                $element->marked = true;
        }
    }

}