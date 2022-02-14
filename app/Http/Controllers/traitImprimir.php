<?php

namespace Intranet\Http\Controllers;

use Intranet\Services\GestorService;
use Jenssegers\Date\Date;
use Intranet\Componentes\Pdf as PDF;
use Styde\Html\Facades\Alert;
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

    /**
     * @param string $modelo
     * @param null $inicial
     * @param null $final
     * @param string $orientacion
     * @param bool $link
     * @return \Illuminate\Http\RedirectResponse
     */

    public function imprimir($modelo = '', $inicial = null, $final = null, $orientacion='portrait', $link=true)
    {
        $modelo = $modelo ?? strtolower($this->model) . 's';
        $final = $final ?? '_print';
        $inicial =  $inicial ?? config('modelos.' . getClass($this->class) . '.print') - 1;
        $todos = $this->class::where('estado', '=', $inicial)->get();
        if ($todos->Count()) {
            $pdf = $this->hazPdf("pdf.$modelo", $todos,null,$orientacion);
            $nom = $this->model . new Date() . '.pdf';
            $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
            $tags = config("modelos.$this->model.documento");
            $gestor = new GestorService();
            $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => $tags ]);
            $this->makeAll($todos, $final);
            if ($link) {
                $this->makeLink($todos,$doc);
            }
            return $pdf->save(storage_path('/app/' . $nomComplet))->download($nom);
        } 
        Alert::info(trans('messages.generic.empty'));
        return back();
        
    }

    /**
     * @param $informe
     * @param $todos
     * @param null $datosInforme
     * @param string $orientacion
     * @param string $dimensiones
     * @param int $margin_top
     * @return mixed

    protected static function hazPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
                                     $margin_top= 15)
    {
        $datosInforme = $datosInforme==null?FechaString(null,'ca'):$datosInforme;
        if (is_string($dimensiones)) {
            return(PDF::loadView($informe, compact('todos', 'datosInforme'))
                    ->setPaper($dimensiones)
                    ->setOrientation($orientacion)
                    ->setOption('margin-top', $margin_top));
        } 
            
        //carnet
        return(PDF::loadView($informe, compact('todos', 'datosInforme'))
                ->setOrientation($orientacion)
                ->setOption('margin-top', 2)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('page-width', $dimensiones[0])
                ->setOption('page-height', $dimensiones[1]));
    }*/

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
