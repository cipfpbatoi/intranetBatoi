<?php

namespace Intranet\Sao\Documents;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura as Firma;
use Intranet\Sao\Support\SaoDownloadManager;
use Intranet\Sao\Support\SaoNavigator;
use Intranet\Services\Signature\DigitalSignatureService;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Gestiona la descàrrega i signatura dels annexes A2 i A3.
 */
class A2DocumentService
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
     * Descarrega i, si cal, firma digitalment l'annex A2/A3.
     *
     * @param AlumnoFct $fctAl
     * @param RemoteWebDriver $driver
     * @param string|null $certPath
     * @param string|null $certPassword
     * @param int $annexeNum
     * @return bool
     */
    public function download(
        AlumnoFct $fctAl,
        RemoteWebDriver $driver,
        ?string $certPath,
        ?string $certPassword,
        int $annexeNum
    ): bool {
        $tmpDirectory = $this->downloadManager->tempDirectory();
        $annexe = $fctAl->Fct->asociacion >= 3 ? 'A' . $annexeNum . 'DUAL' : 'A' . $annexeNum;
        $tmpFile = $tmpDirectory . $annexe . '.pdf';
        $saveFile = $fctAl->routeFile($annexe);
        $x = config('signatures.files.' . $annexe . '.owner.x');
        $y = config('signatures.files.' . $annexe . '.owner.y');
        $ad = (string) config('sao.urls.generate_pdf', 'https://foremp.edu.gva.es/inc/ajax/generar_pdf.php')
            . '?doc=' . $annexeNum . '&centro=59&idFct=' . $fctAl->idSao;

        try {
            $driver->get($ad);
        } catch (\Throwable $exception) {
            Log::info('TMP dir', ['tmpDirectory' => $tmpDirectory, 'tmpFile' => $tmpFile]);
            Log::info('TMP listing', ['files' => glob($tmpDirectory . '*.pdf')]);
            if (file_exists($tmpFile)) {
                if ($certPath) {
                    $this->digitalSignatureService->signDocument(
                        $tmpFile,
                        $saveFile,
                        $x,
                        $y,
                        $certPath,
                        $certPassword
                    );
                    Firma::saveIfNotExists($annexe, $fctAl->idSao, 2);
                } else {
                    copy($tmpFile, $saveFile);
                    Firma::saveIfNotExists($annexe, $fctAl->idSao);
                }
                unlink($tmpFile);
                return true;
            }

            Alert::warning(
                "No s'ha pogut descarregar el fitxer de la FCT Anexe "
                . $annexeNum
                . " $fctAl->idSao de $tmpFile de "
                . $fctAl->Alumno->FullName
            );
            $this->navigator->backToMain($driver);
        }
        return false;
    }
}
