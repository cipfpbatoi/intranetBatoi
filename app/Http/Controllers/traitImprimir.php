<?php

namespace Intranet\Http\Controllers;

use Jenssegers\Date\Date;
use PDF;
use Intranet\Entities\Documento;
use Intranet\Entities\Actividad;
use Styde\Html\Facades\Alert;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use DateTime;
use Illuminate\Support\Facades\Response;
use Intranet\Entities\Profesor;

trait traitImprimir
{

    public function imprime($id, $orientacion = 'portrait')
    {
        $elemento = $this->class::findOrFail($id);
        $informe = 'pdf.' . strtolower($this->model);
        $pdf = $this->hazPdf($informe, $elemento, null, $orientacion);
        return $pdf->stream();
    }

    public function imprimir($modelo = '', $inicial = null, $final = null,$orientacion='portrait')
    {
        $modelo = $modelo ? $modelo : strtolower($this->model) . 's';
        $final = $final ? $final : '_print';
        $todos = $this->class::listos($inicial);
        if ($todos->Count()) {
            $pdf = $this->hazPdf("pdf.$modelo", $todos,null,$orientacion);
            $nom = $this->model . new Date() . '.pdf';
            $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
            $tags = config("modelos.$this->model.documento");
            $doc = Documento::crea(null, ['fichero' => $nomComplet, 'tags' => $tags ]);
            $this->makeAll($todos, $final);
            return $pdf->save(storage_path('/app/' . $nomComplet))->download($nom);
        } else {
            Alert::info(trans('messages.generic.empty'));
            return back();
        }
    }

    protected function hazPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
            $margin_top= 15)
    {
        $datosInforme = $datosInforme==null?FechaString(null,'ca'):$datosInforme;
        
        if (is_string($dimensiones)) {
            $pdf = PDF::loadView($informe, compact('todos', 'datosInforme'))
                    ->setPaper($dimensiones)
                    ->setOrientation($orientacion)
                    ->setOption('margin-top', $margin_top);
        } else {
            $pdf = PDF::loadView($informe, compact('todos', 'datosInforme'))
                    ->setOrientation($orientacion)
                    ->setOption('margin-top', 2)
                    ->setOption('margin-left', 0)
                    ->setOption('margin-right', 0)
                    ->setOption('margin-bottom', 0)
                    ->setOption('page-width', $dimensiones[0])
                    ->setOption('page-height', $dimensiones[1]);
        }

        return($pdf);
    }
    
    //torna arxiu ics per a guardar
    protected function do_ics($id){
        return $this->make_ics($id)->render();
    }
    
    
    // carrega vista ics
    public function ics($id,$descripcion='descripcion',$objetivos='objetivos')
    {
        $vCalendar = $this->make_ics($id,$descripcion,$objetivos);
        return Response::view('ics', compact('vCalendar'))->header('Content-Type', 'text/calendar');
    }
    
    protected function make_ics($id,$descripcion='descripcion',$objetivos='objetivos')
    {
        $elemento = $this->class::findOrFail($id);
        $vCalendar = new Calendar('intranet.cipfpbatoi.app');
        $vEvent = new Event();
        if (isset($elemento->desde)) {
           $ini =  new DateTime($elemento->desde);
           $fin = new DateTime($elemento->hasta);
        } else {
            $ini = new DateTime($elemento->fecha);
            $fin = new DateTime($elemento->fecha);
            $fin->add(new \DateInterval("PT1H"));
        }
        $vEvent->setDtStart($ini)
                ->setDtEnd($fin)
                ->setLocation(config('constants.contacto.nombre'), config('constants.contacto.direccion'))
                ->setSummary(ucfirst($this->model)." : ". $elemento->$descripcion.'')
                ->setDescription($elemento->$objetivos);
        $vCalendar->addComponent($vEvent);
        return $vCalendar;
    }
    protected function cargaDatosCertificado($datos){
        $secretario = Profesor::find(config('constants.contacto.secretario'));
        $director = Profesor::find(config('constants.contacto.director'));
        $datos['fecha'] = FechaString(null,'ca');
        $datos['secretario']['titulo'] = $secretario->sexo == 'H'?'En':'Na';
        $datos['secretario']['articulo'] = $secretario->sexo == 'H'?'El':'La';
        $datos['secretario']['genero'] = $secretario->sexo == 'H'?'secretari':'secretària';
        $datos['secretario']['nombre'] = $secretario->fullName;
        $datos['director']['articulo'] = $director->sexo == 'H'?'El':'La';
        $datos['director']['genero'] = $director->sexo == 'H'?'director':'directora';
        $datos['director']['nombre'] = $director->fullName;
        return $datos;
    }

}
