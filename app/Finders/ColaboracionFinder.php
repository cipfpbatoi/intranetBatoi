<?php


namespace Intranet\Finders;

use Intranet\Entities\Colaboracion;

class ColaboracionFinder implements Finder
{
    private $dni;
    private $estado;

    /**
     * DocFCTService constructor.
     * @param $dni
     * @param $estado
     * @param $document
     */
    public function __construct($dniTutor,$faseColaboracion)
    {
        $this->dni = $dniTutor;
        $this->estado = $faseColaboracion;

    }

    public function exec(){
        $colaboraciones = Colaboracion::MiColaboracion(null,$this->dni)->where('tutor',$this->dni)->where('estado',$this->estado)->get();
        return $colaboraciones;
    }



}