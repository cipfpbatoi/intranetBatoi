<?php

namespace Intranet\Sao;

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;
use Intranet\Componentes\Mensaje;
use Intranet\Exceptions\CertException;
use Intranet\Services\DigitalSignatureService;
use Intranet\Entities\AlumnoFct;
use setasign\Fpdi\Fpdi;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Signatura as Firma;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class A2
{

    const HTTPS_FOREMP_EDU_GVA_ES_INDEX_PHP_OP_2_SUBOP_0 = 'https://foremp.edu.gva.es/index.php?op=2&subop=0';
    private DigitalSignatureService $digitalSignatureService;

    public function __construct(DigitalSignatureService $digitalSignatureService)
    {
        $this->digitalSignatureService = $digitalSignatureService;
    }
    public static function setFireFoxCapabilities()
    {
        $profile = new FirefoxProfile();
        $profile->setPreference('browser.download.folderList', 2);
        $profile->setPreference('browser.download.dir', '/Users/igomis/code/intranetBatoi/storage/tmp');
        $profile->setPreference('browser.helperApps.neverAsk.saveToDisk', 'application/pdf');
        $profile->setPreference('pdfjs.enabledCache.state', false);
        $profile->setPreference('modifyheaders.headers.count', 1);
        $profile->setPreference("modifyheaders.headers.action0", "Add");
            # Set here the name of the header
        $profile->setPreference("modifyheaders.headers.name0", "Content-Disposition");
            # Set here the value of the header
        $profile->setPreference("modifyheaders.headers.value0", "inline");
        $profile->setPreference("modifyheaders.headers.enabled0", true);
        $profile->setPreference("modifyheaders.config.active", true);
        $profile->setPreference("modifyheaders.config.alwaysOn", true);

        if (config('services.selenium.firefox_path')) {
            $caps = DesiredCapabilities::firefox()->setCapability(FirefoxOptions::CAPABILITY,
                ['binary' => config('services.selenium.firefox_path')]);
        } else {
            $caps = DesiredCapabilities::firefox();
        }
        $caps->setCapability('firefox_profile', $profile);
        return $caps;
    }

    private static function waitForFile($filePath, $timeout)
    {
        $startTime = time();
        while (!file_exists($filePath)) {
            if (time() - $startTime > $timeout) {
                throw new \Exception("Timeout waiting for file: $filePath");
            }
            sleep(1);
        }
    }



    public function index($driver, $request, $file = null)
    {
        $driver->manage()->timeouts()->pageLoadTimeout(2);
        $fcts = array_keys($request, 'on');
        $decrypt = $request['decrypt'] ?? null;
        $passCert = $request['cert'] ?? null;
        $nomFitxer = storage_path('tmp/' . authUser()->fileName . '.pfx');

        try {
            if (isset($decrypt)) {
                $this->digitalSignatureService->decryptUserCertificateInstance($decrypt, authUser());
                $cert = $this->digitalSignatureService->readCertificate($nomFitxer, $passCert);
            }

            if ($file) {
                $file->move(dirname($nomFitxer), basename($nomFitxer));
                @unlink($file->getRealPath());
                $cert = $this->digitalSignatureService->readCertificate($nomFitxer, $passCert);
            }
            $this->downloadFilesFromFcts($driver, $fcts, $cert ?? null);
            if (file_exists($nomFitxer)) {
                unlink($nomFitxer);
            }
        } catch (CertException $exception) {
            Log::channel('certificate')->alert($exception->getMessage(), [
                'intranetUser' => authUser()->fullName,
            ]);
            Alert::warning($exception->getMessage());
            Mensaje::send(
                config('avisos.errores'),
                $exception->getMessage() . " : " . authUser()->fullName
            );
            if (file_exists($nomFitxer)) {
                unlink($nomFitxer);
            }
            $driver->quit();
            return back();
        }
        $driver->quit();
        return back();
    }



    public function downloadFilesFromFcts(RemoteWebDriver $driver, $fcts, $certFile=null)
    {
        $signat = false;
        $a1 = $a2 = $a3 = $fA1 = $a5 = false;

        foreach ($fcts as $fct) {
            if ($fct === 'FA1'){ //A1 forçat
                $fA1 = true;
            }
            if ($fct === 'A1') { //A1 dèbil
                $a1 = true;
            }
            if ($fct === 'A2') { //A2 forçat
                $a2 = true;
            }
            if ($fct === 'A3') { //A3 forçat
                $a3 = true;
            }
            if ($fct === 'A5') {
                $a5 = true;
            }
            $fctAl = AlumnoFct::find($fct);
            if ($fctAl){
                // Anexe 1
                if ($fA1 || ($a1 && ($fctAl->Fct->Colaboracion->Centro->Empresa->ConveniCaducat
                    || $fctAl->Fct->Colaboracion->Centro->Empresa->RenovatConveni))) {
                    $signat = $this->annexe1($fctAl, $driver);
                }
                // Anexe 2
                if ($a2 && $this->annexe23($fctAl, $driver, $certFile,2)) {
                    $signat = true;
                }
                // Anexe 3
                if ($a3 && $certFile){
                    $this->annexe23($fctAl, $driver, $certFile,3);
                }
                if ($a5) {
                    $this->annexe5($fctAl, $driver, $certFile);
                }
            }
        }
        if ($signat) {
            Mensaje::send(
                config('avisos.director'),
                'Tens nous documents per signar de '.authUser()->fullName,
                '/direccion/signatures'
            );
        }
    }

    /**
     * @param $fctAl
     * @param  RemoteWebDriver  $driver
     * @param  mixed  $tmpDirectory
     * @param  bool  $signat
     * @return array
     */
    public function annexe1(AlumnoFct $fctAl, RemoteWebDriver $driver): bool
    {
        $tmpDirectory = config('variables.shareDirectory') ?? storage_path('tmp/');
        $doc = $fctAl->Fct->dual ? '201' : '101';
        $annexe = $fctAl->Fct->dual ? 'A1DUAL' : 'A1';
        $idSao = $fctAl->Fct->Colaboracion->Centro->idSao;
        $tmpFile = "$tmpDirectory$annexe.pdf";
        $saveFile = $fctAl->routeFile($annexe);

        try {
            $driver->get("https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc=$doc&centro=59&ct=$idSao");
        } catch (\Throwable $exception) {
            Log::error("Error en la generació del PDF per $annexe: " . $exception->getMessage());
        }

        if (file_exists($tmpFile)) {
            copy($tmpFile, $saveFile);
            Firma::saveIfNotExists($annexe, $fctAl->idSao);
            unlink($tmpFile);
        }

        $driver->get(self::HTTPS_FOREMP_EDU_GVA_ES_INDEX_PHP_OP_2_SUBOP_0);
        sleep(1);

        return true;
    }

    /**
     * @param $fctAl
     * @param  RemoteWebDriver  $driver
     * @param  mixed  $tmpDirectory
     * @param  mixed  $certFile
     * @param  bool  $signat
     * @return array
     * @throws \Intranet\Exceptions\IntranetException
     */
    private function annexe23(
        $fctAl,
        RemoteWebDriver $driver,
        mixed $certFile,
        $anexeNum
    ): bool {
        $tmpDirectory = config('variables.shareDirectory')??storage_path('tmp/');
        $annexe = $fctAl->Fct->asociacion >= 3 ? 'A'.$anexeNum.'DUAL' : 'A'.$anexeNum;
        $tmpFile = $tmpDirectory.$annexe.".pdf";
        $saveFile = $fctAl->routeFile($annexe);
        $x = config("signatures.files.".$annexe.".owner.x");
        $y = config("signatures.files.".$annexe.".owner.y");
        $ad = "https://foremp.edu.gva.es/inc/ajax/generar_pdf.php".
            "?doc=".$anexeNum."&centro=59&idFct=$fctAl->idSao";
        try {
            $driver->get($ad);
        } catch (\Throwable $exception) {
            if (file_exists($tmpFile)) {
                if ($certFile) {
                    $this->digitalSignatureService->signDocument(
                        $tmpFile,
                        $saveFile,
                        $x,
                        $y,
                        $certFile
                    );
                    Firma::saveIfNotExists($annexe, $fctAl->idSao, 2);
                } else {
                    copy($tmpFile, $saveFile);
                    Firma::saveIfNotExists($annexe, $fctAl->idSao);
                }
                unlink($tmpFile);
                return true;
            }

            Alert::warning("No s'ha pogut descarregar el fitxer de la FCT Anexe II
                  $fctAl->idSao de $tmpFile");
            $driver->get(self::HTTPS_FOREMP_EDU_GVA_ES_INDEX_PHP_OP_2_SUBOP_0);
            sleep(1);
        }
        return false;
    }

    private  function annexe5($fctAl, RemoteWebDriver $driver,$certFile):bool
    {
        $tmpDirectory = config('variables.shareDirectory')??storage_path('tmp/');
        $doc = $fctAl->Fct->dual ? '201' : '101';
        $annexe = $fctAl->Fct->dual ? 'A5DUAL' : 'A5';
        $idSao = $fctAl->idSao;
        $tmpFile = $tmpDirectory.$annexe.".pdf";
        $tmp1File = $tmpDirectory.$annexe."(1).pdf";
        $saveFile = $fctAl->routeFile($annexe);
        $x = config("signatures.files.".$annexe.".owner.x");
        $y = config("signatures.files.".$annexe.".owner.y");
        $error = false;
        try {
            $driver->get("https://foremp.edu.gva.es/index.php?accion=7&idFct=$idSao");
            sleep(1);
            $enlace = $driver->findElement(WebDriverBy::xpath("//a[contains(@class, 'enlDocFCT') and contains(., 'Informe de Consecución de Competencias (Autorrellenable)')]"));
            $enlace->click();
            sleep(1);
            $printButton = $driver->findElement(WebDriverBy::xpath("//button[contains(.,'Imprimir documento')]"));
            $printButton->click();
            sleep(1);
            self::waitForFile($tmpFile, 5);

            $pdf = new FPDI();
            $pageCount = $pdf->setSourceFile($tmpFile);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $tplIdx = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplIdx);
                $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);

                $pdf->useTemplate($tplIdx);
                $contacto = $fctAl->valoracio;
                if ($pageNo == $pageCount && $contacto) {
                    // Estableix la font i el tamany
                    $pdf->SetFont('Helvetica', '', 9);

                    // Defineix la posició on afegir el text (x, y)
                    $x1 = 10;
                    $y1 = 175;

                    $valoracioText = str_replace("'", "&#39;", $fctAl->valoracio);
                    $valoracioText = mb_convert_encoding($valoracioText, 'ISO-8859-1', 'UTF-8');
                    $valoracioText = str_replace("&#39;", "'", $valoracioText);

                    // Afegeix el text a la posició especificada amb MultiCell per gestionar el canvi de línia
                    $pdf->SetXY($x1, $y1);

                    // Defineix l'amplada de la cel·la i la interlínia (height)
                    $cellWidth = 190; // Amplada de la cel·la (ajusta segons les teves necessitats)
                    $lineHeight = 5; // Alçada de la línia

                    // Afegeix el text a la posició especificada
                    $pdf->MultiCell($cellWidth, $lineHeight, $valoracioText, 0, 'L');
                }
            }
            $pdf->Output($tmp1File, 'F');
            // Copia el fitxer descarregat al destí
        } catch (\Throwable $exception) {
            Alert::danger($exception->getMessage().' en Annex 5 de '.$fctAl->Alumno->FullName );
            $error = true;

        } finally {

            if (!$error) {
                if ($certFile) {
                    $this->digitalSignatureService->signDocument(
                        $tmp1File,
                        $saveFile,
                        $x,
                        $y,
                        $certFile
                    );
                    Firma::saveIfNotExists($annexe, $fctAl->idSao, 2);
                } else {
                    copy($tmp1File, $saveFile);
                    Firma::saveIfNotExists($annexe, $fctAl->idSao);
                }
            }
            if (file_exists($tmpFile)) {
                unlink($tmpFile);
            }
            $driver->get(self::HTTPS_FOREMP_EDU_GVA_ES_INDEX_PHP_OP_2_SUBOP_0);
            sleep(1);
        }
        return true;
    }

}
