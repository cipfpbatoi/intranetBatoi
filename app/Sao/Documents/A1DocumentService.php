<?php

namespace Intranet\Sao\Documents;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura as Firma;
use Intranet\Sao\Support\SaoDownloadManager;
use Intranet\Sao\Support\SaoNavigator;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Gestiona la descàrrega de l'annex A1/A1DUAL.
 */
class A1DocumentService
{
    private SaoNavigator $navigator;
    private SaoDownloadManager $downloadManager;

    public function __construct(?SaoNavigator $navigator = null, ?SaoDownloadManager $downloadManager = null)
    {
        $this->navigator = $navigator ?? new SaoNavigator();
        $this->downloadManager = $downloadManager ?? new SaoDownloadManager();
    }

    /**
     * Descarrega l'annex A1/A1DUAL.
     *
     * Manté el comportament històric:
     * - retorna `true` en camí d'excepció (quan detecta o no fitxer temporal)
     * - retorna `false` si la càrrega no llança excepció.
     *
     * @param AlumnoFct $fctAl
     * @param RemoteWebDriver $driver
     * @return bool
     */
    public function download(AlumnoFct $fctAl, RemoteWebDriver $driver): bool
    {
        $tmpDirectory = $this->downloadManager->tempDirectory();
        $doc = $fctAl->Fct->dual ? '201' : '101';
        $annexe = $fctAl->Fct->dual ? 'A1DUAL' : 'A1';
        $idSao = $fctAl->Fct->Colaboracion->Centro->idSao;
        $tmpFile = "$tmpDirectory$annexe.pdf";
        $saveFile = $fctAl->routeFile($annexe);
        $generatePdfUrl = (string) config('sao.urls.generate_pdf', 'https://foremp.edu.gva.es/inc/ajax/generar_pdf.php');

        try {
            $driver->get("$generatePdfUrl?doc=$doc&centro=59&ct=$idSao");
        } catch (\Throwable $exception) {
            if (file_exists($tmpFile)) {
                copy($tmpFile, $saveFile);
                Firma::saveIfNotExists($annexe, $fctAl->idSao);
                unlink($tmpFile);
            } else {
                Log::error(
                    'Error en la generació del PDF per A1: '
                    . $exception->getMessage()
                    . ' '
                    . $generatePdfUrl
                    . '?doc='
                    . $doc
                    . '&centro=59&ct='
                    . $idSao
                );
                Alert::warning(
                    "No s'ha pogut descarregar el fitxer de la FCT Anexe I "
                    . "$fctAl->idSao de $tmpFile: $doc de "
                    . $fctAl->Alumno->FullName
                );
            }

            $this->navigator->backToMain($driver);

            return true;
        }

        return false;
    }
}
