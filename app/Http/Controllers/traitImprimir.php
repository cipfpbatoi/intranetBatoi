<?php

namespace Intranet\Http\Controllers;

use Intranet\Services\GestorService;
use Intranet\Componentes\Pdf as PDF;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use DateTime;
use Illuminate\Support\Facades\Response;


/**
 * Trait traitImprimir
 * @package Intranet\Http\Controllers
 */
trait traitImprimir
{

    /**
     * @param $id
     * @param string $orientacion
     * @return mixed
     */
    public function imprime($id, $orientacion = 'portrait')
    {
        $elemento = $this->class::findOrFail($id);
        $informe = 'pdf.' . strtolower($this->model);
        $pdf = $this->hazPdf($informe, $elemento, null, $orientacion);
        return $pdf->stream();
    }

    protected static function hazPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
                                     $margin_top= 15)
    {

        return PDF::hazPdf($informe, $todos, $datosInforme , $orientacion , $dimensiones , $margin_top );
    }


    /**
     @param $id
     @return string
     **/
    protected function do_ics($id){
        return $this->make_ics($id)->render();
    }
    
    

    /**
      @param $id
      @param string $descripcion
      @param string $objetivos
      @return \Illuminate\Http\Response
     **/
    public function ics($id, $descripcion='descripcion', $objetivos='objetivos')
    {
        $vCalendar = $this->make_ics($id,$descripcion,$objetivos);
        return Response::view('ics', compact('vCalendar'))->header('Content-Type', 'text/calendar');
    }

    /**
     * @param $id
     * @param string $descripcion
     * @param string $objetivos
     * @return Calendar
     * @throws \Exception
     */
    public function make_ics($id, $descripcion='descripcion', $objetivos='objetivos')
    {

        $elemento = $this->class::findOrFail($id);

        if (isset($elemento->desde)) {
           $ini =  new DateTime($elemento->desde);
           $fin = new DateTime($elemento->hasta);
        } else {
            $ini = new DateTime($elemento->fecha);
            $fin = new DateTime($elemento->fecha);
            $fin->add(new \DateInterval("PT1H"));
        }
        return $this->build_ics($ini,$fin,ucfirst($this->model)." : ". $elemento->$descripcion,$elemento->$objetivos,config('contacto.nombre'));

    }

    public function build_ics($ini,$fin,$descripcion,$objetivos,$location){
        $vCalendar = new Calendar('intranet.cipfpbatoi.es');
        $vEvent = new Event();
        $vEvent->setDtStart($ini)
            ->setDtEnd($fin)
            ->setLocation($location)
            ->setSummary( $descripcion)
            ->setDescription($objetivos);
        $vCalendar->addComponent($vEvent);
        return $vCalendar;
    }

    public function gestor($id)
    {
        $gestor = new GestorService($this->class::findOrFail($id));
        return $gestor->render();
    }



}
