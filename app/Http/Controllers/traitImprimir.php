<?php

namespace Intranet\Http\Controllers;

use Intranet\Services\AdviseTeacher;
use Intranet\Services\CalendarService;
use Intranet\Services\GestorService;
use Intranet\Componentes\Pdf as PDF;
use Illuminate\Support\Facades\Response;


/**
 * Trait traitImprimir
 * @package Intranet\Http\Controllers
 */
trait traitImprimir
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
     * @return void
     */
    protected static function hazPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
                                     $margin_top= 15)
    {
        return PDF::hazPdf($informe, $todos, $datosInforme , $orientacion , $dimensiones , $margin_top );
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
        $gestor = new GestorService($this->class::findOrFail($id));
        return $gestor->render();
    }



}
