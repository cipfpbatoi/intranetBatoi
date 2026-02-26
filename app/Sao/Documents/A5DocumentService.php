<?php

namespace Intranet\Sao\Documents;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura as Firma;
use Intranet\Sao\Support\SaoDownloadManager;
use Intranet\Sao\Support\SaoNavigator;
use Intranet\Services\Signature\DigitalSignatureService;
use setasign\Fpdi\Fpdi;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Gestiona la descàrrega i processat de l'annex A5.
 */
class A5DocumentService
{
    private DigitalSignatureService $digitalSignatureService;
    private SaoNavigator $navigator;
    private SaoDownloadManager $downloadManager;

    public function __construct(
        DigitalSignatureService $digitalSignatureService,
        ?SaoNavigator $navigator = null,
        ?SaoDownloadManager $downloadManager = null
    )
    {
        $this->digitalSignatureService = $digitalSignatureService;
        $this->navigator = $navigator ?? new SaoNavigator();
        $this->downloadManager = $downloadManager ?? new SaoDownloadManager();
    }

    /**
     * Descarrega, processa i opcionalment firma l'annex A5.
     *
     * @param AlumnoFct $fctAl
     * @param RemoteWebDriver $driver
     * @param string|null $certPath
     * @param string|null $certPassword
     * @return bool
     */
    public function download(
        AlumnoFct $fctAl,
        RemoteWebDriver $driver,
        ?string $certPath,
        ?string $certPassword
    ): bool {
        $tmpDirectory = $this->downloadManager->tempDirectory();
        $annexe = $fctAl->Fct->asociacion >= 3 ? 'A5DUAL' : 'A5';
        $idSao = $fctAl->idSao;
        $tmpFile = $tmpDirectory . $annexe . '.pdf';
        $tmp1File = $tmpDirectory . $annexe . '(1).pdf';
        $saveFile = $fctAl->routeFile($annexe);
        $x = config('signatures.files.' . $annexe . '.owner.x');
        $y = config('signatures.files.' . $annexe . '.owner.y');
        $error = false;
        $fctUrlBase = (string) config('sao.urls.fct', 'https://foremp.edu.gva.es/index.php?accion=7&idFct=');

        try {
            $driver->get($fctUrlBase . $idSao);
            sleep(1);
            $enlace = $driver->findElement(
                WebDriverBy::xpath(
                    "//a[contains(@class, 'enlDocFCT') and contains(., 'Informe de Consecución de Competencias (Autorrellenable)')]"
                )
            );
            $enlace->click();
            sleep(1);
            $printButton = $driver->findElement(WebDriverBy::xpath("//button[contains(.,'Imprimir documento')]"));
            $printButton->click();
            sleep(1);
            $this->downloadManager->waitForFile($tmpFile, (int) config('sao.download.wait_seconds', 10));

            $pdf = new FPDI();
            $pageCount = $pdf->setSourceFile($tmpFile);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplIdx);
                $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);

                $pdf->useTemplate($tplIdx);
                $contacto = $fctAl->valoracio;
                if ($pageNo == $pageCount && $contacto) {
                    $pdf->SetFont('Helvetica', '', 9);
                    $x1 = 10;
                    $y1 = 175;

                    $valoracioText = str_replace("'", '&#39;', $fctAl->valoracio);
                    $valoracioText = mb_convert_encoding($valoracioText, 'ISO-8859-1', 'UTF-8');
                    $valoracioText = str_replace('&#39;', "'", $valoracioText);

                    $pdf->SetXY($x1, $y1);
                    $cellWidth = 190;
                    $lineHeight = 5;
                    $pdf->MultiCell($cellWidth, $lineHeight, $valoracioText, 0, 'L');
                }
            }
            $pdf->Output($tmp1File, 'F');
        } catch (\Throwable $exception) {
            Alert::danger($exception->getMessage() . ' en Annex 5 de ' . $fctAl->Alumno->FullName);
            $error = true;
        } finally {
            if (!$error) {
                if ($certPath) {
                    $this->digitalSignatureService->signDocument(
                        $tmp1File,
                        $saveFile,
                        $x,
                        $y,
                        $certPath,
                        $certPassword
                    );
                    Firma::saveIfNotExists($annexe, $fctAl->idSao, 2);
                } else {
                    copy($tmp1File, $saveFile);
                    Firma::saveIfNotExists($annexe, $fctAl->idSao);
                }
            }
            $this->downloadManager->unlinkIfExists($tmpFile);
            $this->navigator->backToMain($driver);
        }

        return true;
    }
}
