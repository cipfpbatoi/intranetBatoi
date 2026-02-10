<?php

namespace Intranet\Http\Traits;

use Illuminate\Support\Facades\Response;
use Intranet\Services\Document\PdfService;
use Intranet\Services\Notifications\AdviseTeacher;
use Intranet\Services\Calendar\CalendarService;
use Intranet\Services\General\GestorService;


/**
 * Trait traitImprimir
 * @package Intranet\Http\Controllers
 */
trait Imprimir
{

    protected function notify($id)
    {
        AdviseTeacher::exec($this->class::findOrFail($id));
        return back();
    }
    /**
     * @param $informe
     * @param $todos
     * @param $datosInforme
     * @param $orientacion
     * @param $dimensiones
     * @param $margin_top
     */
    protected static function hazPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
                                     $margin_top= 15)
    {
        return app(PdfService::class)->hazPdf($informe, $todos, $datosInforme , $orientacion , $dimensiones , $margin_top );
    }

    /**
      @param $id
      @param string $descripcion
      @param string $objetivos
      @return \Illuminate\Http\Response
     **/
    public function ics($id, $descripcion='descripcion', $objetivos='objetivos')
    {
        $elemento = $this->class::findOrFail($id);
        $vCalendar = CalendarService::build($elemento,$descripcion,$objetivos);
        return Response::view('ics', compact('vCalendar'))->header('Content-Type', 'text/calendar');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function gestor($id)
    {
        return (new GestorService($this->class::findOrFail($id)))->render();
    }



}
