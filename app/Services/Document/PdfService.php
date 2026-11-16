<?php

namespace Intranet\Services\Document;

use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;
use Intranet\Services\Document\ZipService;
use Illuminate\Support\Facades\Log;
use Intranet\Services\UI\AppAlert as Alert;
use function config;
use function env;
use function fechaString;


/**
 * Servei de generació de PDFs i ZIPs amb els recursos gràfics de cada centre.
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
            $this->loadSnappyView(
                $informe,
                compact('todos', 'datosInforme')
            )->setPaper($dimensiones)
                ->setOrientation($orientacion)
                ->setOption('margin-top', $marginTop)
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
        $datosInforme = $datosInforme==null?fechaString(null, 'ca'):$datosInforme;
        if (is_string($dimensiones)) {
            return($this->loadSnappyView(
                $informe,
                compact('todos', 'datosInforme')
                )->setPaper($dimensiones)
                ->setOrientation($orientacion)
                ->setOption('margin-top', $marginTop)
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
            $html = view($informe, compact('todos', 'datosInforme'))->render();
            if ($this->hasCentreFooter($html)) {
                $pie = $this->footerText($informe);
                $footer = '<style>@page { margin-bottom: 28mm; }</style>'
                    . '<div style="position: fixed; bottom: -23mm; left: 0; right: 0;">'
                    . view('pdf.partials.imatgePeuCentre', compact('pie'))->render() . '</div>';
                $html = preg_replace_callback('/<body\b[^>]*>/i', fn ($match) => $match[0] . $footer, $html, 1);
            }
            return(DomPDF::loadHTML($html)
                ->setPaper($dimensiones, $orientacion));
        }
    }

    /**
     * Carrega la vista i configura un peu natiu repetit en cada pàgina de Snappy.
     *
     * @param string $informe
     * @param array $data
     * @return \Barryvdh\Snappy\PdfWrapper
     */
    protected function loadSnappyView($informe, array $data)
    {
        $html = view($informe, $data)->render();
        $pie = $this->footerText($informe);
        $pdf = SnappyPDF::loadHTML($html);

        if ($this->hasCentreFooter($html)) {
            return $pdf->setOption('footer-html', view('pdf.partials.pieCentre', compact('pie'))->render())
                ->setOption('margin-bottom', 28)
                ->setOption('footer-spacing', 5)
                ->setOption('footer-line', false)
                ->setOption('footer-right', '');
        }

        return $pdf->setOption('footer-line', true)->setOption('footer-right', $pie);
    }

    /**
     * Comprova si el document usa la capçalera comuna i el centre té imatge de peu.
     *
     * @param string $html
     * @return bool
     */
    protected function hasCentreFooter($html)
    {
        return str_contains($html, 'data-centre-header') && is_file(public_path('img/pdf/pie.png'));
    }
}
