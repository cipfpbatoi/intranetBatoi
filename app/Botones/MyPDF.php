<?php


namespace Intranet\Botones;


use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Illuminate\Support\Facades\Response;
use Intranet\Botones\Pdf as PDF;
use Intranet\Services\Gestor;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;

class MyPDF
{
    private $model;
    private $elements;
    private $features;


    public function __construct(){

    }
}

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
    $modelo = $modelo ? $modelo : strtolower($this->model) . 's';
    $final = $final ? $final : '_print';
    $todos = $this->class::listos($inicial);
    if ($todos->Count()) {
        $pdf = $this->hazPdf("pdf.$modelo", $todos,null,$orientacion);
        $nom = $this->model . new Date() . '.pdf';
        $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
        $tags = config("modelos.$this->model.documento");
        $gestor = new Gestor();
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
    $gestor = new Gestor($this->class::findOrFail($id));
    return $gestor->render();
}

