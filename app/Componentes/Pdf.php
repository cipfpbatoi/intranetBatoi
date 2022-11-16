<?php

namespace Intranet\Componentes;

use Barryvdh\DomPDF\Facade as DomPDF;
use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;
use function config;
use function env;
use function fechaString;


class Pdf
{

    private static function pie($informe){
        $rutaDesglosada = explode('.',$informe);
        $document = end($rutaDesglosada);
        $pie = config('footers.'.$document);
        if (isset($pie)){
            return  "Codi: ".$pie['codi']."  - Num. edicio: ".$pie['edicio'];
        }
        return "";
    }

    public static function hazPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
                                  $margin_top= 15,$driver=null){
        $driver = $driver??env('PDF_DRIVER', 'SnappyPdf');
        if ($driver==='DomPdf'){
            return self::hazDomPdf($informe, $todos, $datosInforme , $orientacion , $dimensiones );
        }
        if ($driver==='SnappyPdf'){
            return self::hazSnappyPdf($informe, $todos, $datosInforme , $orientacion , $dimensiones , $margin_top);
        }
    }
    protected static function hazSnappyPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
                                     $margin_top= 15)
    {
        $pie = self::pie($informe);
        $datosInforme = $datosInforme==null?fechaString(null,'ca'):$datosInforme;
        if (is_string($dimensiones)) {
            return(SnappyPDF::loadView($informe, compact('todos', 'datosInforme'))
                ->setPaper($dimensiones)
                ->setOrientation($orientacion)
                ->setOption('margin-top', $margin_top)
                ->setOption('footer-line',true)
                ->setOption('footer-right',$pie)
                ->setOption('enable-external-links' , true));
        }

        return(SnappyPdf::loadView($informe, compact('todos', 'datosInforme'))
            ->setOrientation($orientacion)
            ->setOption('margin-top', 2)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('page-width', $dimensiones[0])
            ->setOption('page-height', $dimensiones[1]));
    }

    protected static function hazDomPdf($informe, $todos, $datosInforme , $orientacion , $dimensiones )
    {
        $datosInforme = $datosInforme==null?fechaString(null,'ca'):$datosInforme;
        if (is_string($dimensiones)) {
            return(DomPDF::loadView($informe, compact('todos', 'datosInforme'))
                ->setPaper($dimensiones,$orientacion));
        }
    }
}