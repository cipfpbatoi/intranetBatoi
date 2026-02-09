<?php

namespace Intranet\Services\Document;

use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;
use Intranet\Services\Document\ZipService;
use Illuminate\Support\Facades\Log;
use Styde\Html\Facades\Alert;
use function config;
use function env;
use function fechaString;


/**
 * Servei de generació de PDFs i ZIPs.
 */
class PdfService
{

    /**
     * Calcula el text del peu segons el document.
     *
     * @param string $informe
     * @return string
     */
    public function footerText($informe)
    {
        $rutaDesglosada = explode('.', $informe);
        $document = end($rutaDesglosada);
        $pie = config('footers.'.$document);
        if (isset($pie)) {
            return  "Codi: ".$pie['codi']."  - Num. edicio: ".$pie['edicio'];
        }
        return "";
    }

    /**
     * Genera un PDF amb el driver indicat.
     *
     * @param string $informe
     * @param mixed $todos
     * @param mixed $datosInforme
     * @param string $orientacion
     * @param string|array $dimensiones
     * @param int $marginTop
     * @param string|null $driver
     * @return mixed
     */
    public function hazPdf(
        $informe,
        $todos,
        $datosInforme = null,
        $orientacion = 'portrait',
        $dimensiones = 'a4',
        $marginTop = 15,
        $driver = null
    )
    {
        $driver = $driver??env('PDF_DRIVER', 'SnappyPdf');

        if ($driver==='DomPdf') {
            return $this->hazDomPdf($informe, $todos, $datosInforme, $orientacion, $dimensiones);
        }
        if ($driver==='SnappyPdf') {
            return $this->hazSnappyPdf($informe, $todos, $datosInforme, $orientacion, $dimensiones, $marginTop);
        }
    }




    /**
     * Genera un ZIP amb PDFs per a cada element.
     *
     * @param string $informe
     * @param iterable $all
     * @param mixed $datosInforme
     * @param string $orientacion
     * @param string $field
     * @return string|null
     */
    public function hazZip($informe, $all, $datosInforme = null, $orientacion = 'portrait', $field = 'id')
    {
        $pdfs = [];
        $pie = $this->footerText($informe);
        $className = 'seguiments';
        $datosInforme = $datosInforme ?? fechaString(null, 'ca');
        $dimensiones = 'a4';
        $marginTop = 15;

        foreach ($all as $element) {
            $className = strtolower(str_replace('Intranet\Entities\\', '', get_class($element)));

            // Generar y guardar cada PDF en la carpeta temporal
            $filePath = storage_path("tmp/{$element->$field}.pdf");
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $todos = [$element];
            //
            SnappyPDF::loadView(
                $informe,
                compact('todos', 'datosInforme')
            )->setPaper($dimensiones)
                ->setOrientation($orientacion)
                ->setOption('margin-top', $marginTop)
                ->setOption('footer-line', true)
                ->setOption('footer-right', $pie)
                ->setOption('enable-external-links', true)->save($filePath);
            $pdfs[] = $filePath;
        }

        // Generar el archivo zip con los PDFs generados
        try {
            $zipPath = ZipService::exec($pdfs, $className.'_' . authUser()->dni);
        } catch (\Throwable $e) {
            Log::error('Error generant ZIP', ['message' => $e->getMessage()]);
            Alert::danger('No s\'ha pogut generar el ZIP');
            return null;
        }


        // Retornar la ruta del zip generado
        return storage_path($zipPath);
    }



    /**
     * Genera un PDF amb Snappy.
     *
     * @param string $informe
     * @param mixed $todos
     * @param mixed $datosInforme
     * @param string $orientacion
     * @param string|array $dimensiones
     * @param int $marginTop
     * @return mixed
     */
    protected function hazSnappyPdf(
        $informe,
        $todos,
        $datosInforme = null,
        $orientacion = 'portrait',
        $dimensiones = 'a4',
        $marginTop = 15
    )
    {
        $pie = $this->footerText($informe);
        $datosInforme = $datosInforme==null?fechaString(null, 'ca'):$datosInforme;
        if (is_string($dimensiones)) {
            return(SnappyPDF::loadView(
                $informe,
                compact('todos', 'datosInforme')
                )->setPaper($dimensiones)
                ->setOrientation($orientacion)
                ->setOption('margin-top', $marginTop)
                ->setOption('footer-line', true)
                ->setOption('footer-right', $pie)
                ->setOption('enable-external-links', true));
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

    /**
     * Genera un PDF amb DomPDF.
     *
     * @param string $informe
     * @param mixed $todos
     * @param mixed $datosInforme
     * @param string $orientacion
     * @param string|array $dimensiones
     * @return mixed
     */
    protected function hazDomPdf($informe, $todos, $datosInforme, $orientacion, $dimensiones)
    {

        $datosInforme = $datosInforme==null?fechaString(null, 'ca'):$datosInforme;
        if (is_string($dimensiones)) {
            return(DomPDF::loadView($informe, compact('todos', 'datosInforme'))
                ->setPaper($dimensiones, $orientacion));
        }
    }
}
