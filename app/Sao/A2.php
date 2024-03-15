<?php

namespace Intranet\Sao;

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Log;
use Intranet\Componentes\Mensaje;
use Intranet\Exceptions\CertException;
use Intranet\Services\DigitalSignatureService;
use Intranet\Entities\AlumnoFct;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Signatura as Firma;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class A2
{

    const HTTPS_FOREMP_EDU_GVA_ES_INDEX_PHP_OP_2_SUBOP_0 = 'https://foremp.edu.gva.es/index.php?op=2&subop=0';

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



    public static function index($driver,$request,$file = null)
    {
        $driver->manage()->timeouts()->pageLoadTimeout(2);
        $fcts = array_keys($request, 'on');
        $decrypt = $request['decrypt']??null;
        $passCert = $request['cert']??null;
        $nomFitxer = storage_path('tmp/'.authUser()->fileName.'.pfx');

        try {
            if (isset($decrypt)) {
                DigitalSignatureService::decryptCertificateUser($decrypt, authUser());
                $cert = DigitalSignatureService::readCertificat($nomFitxer, $passCert);
            }
            if ($file) {
                $file->move(dirname($nomFitxer), basename($nomFitxer));
                @unlink($file->getRealPath());
                $cert = DigitalSignatureService::readCertificat($nomFitxer, $passCert);
            }
            self::downloadFilesFromFcts($driver, $fcts, $cert??null);
            if (file_exists($nomFitxer)){
                unlink($nomFitxer);
            }

        } catch (CertException $exception){
            Log::channel('certificate')->alert($exception->getMessage(), [
                'intranetUser' => authUser()->fullName,
            ]);
            Alert::warning($exception->getMessage());
            Mensaje::send(
                config('avisos.errores'),
                $exception->getMessage()." : ".authUser()->fullName
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


    public static function downloadFilesFromFcts(RemoteWebDriver $driver, $fcts, $certFile=null)
    {
        $signat = false;
        $a1 = $a2 = $a3 = $fA1 = false;

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
            $fctAl = AlumnoFct::find($fct);
            if ($fctAl){
                // Anexe 1
                if ($fA1 || ($a1 && ($fctAl->Fct->Colaboracion->Centro->Empresa->ConveniCaducat
                    || $fctAl->Fct->Colaboracion->Centro->Empresa->RenovatConveni))) {
                    $signat = self::annexe1($fctAl, $driver);
                }
                // Anexe 2
                if ($a2 && self::annexe23($fctAl, $driver, $certFile,2)) {
                    $signat = true;
                }
                // Anexe 3
                if ($a3 && $certFile){
                    self::annexe23($fctAl, $driver, $certFile,3);
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
    private static function annexe1($fctAl, RemoteWebDriver $driver):bool
    {
        $tmpDirectory = config('variables.shareDirectory')??storage_path('tmp/');
        $doc = $fctAl->Fct->dual ? '201' : '101';
        $annexe = $fctAl->Fct->dual ? 'A1DUAL' : 'A1';
        $idSao = $fctAl->Fct->Colaboracion->Centro->idSao;
        $tmpFile = $tmpDirectory.$annexe.".pdf";
        $saveFile = $fctAl->routeFile($annexe);
        try {
            $driver->get("https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc=$doc&centro=59&ct=$idSao");
        } catch (\Throwable $exception) {
           if (file_exists($tmpFile)) {
                copy($tmpFile, $saveFile);
            }
            Firma::saveIfNotExists($annexe, $fctAl->idSao);
            unlink($tmpFile);
            $driver->get(self::HTTPS_FOREMP_EDU_GVA_ES_INDEX_PHP_OP_2_SUBOP_0);
            sleep(1);
        }
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
    private static function annexe23(
        $fctAl,
        RemoteWebDriver $driver,
        mixed $certFile,
        $anexeNum
    ): bool {
        $tmpDirectory = config('variables.shareDirectory')??storage_path('tmp/');
        $annexe = $fctAl->Fct->dual ? 'A'.$anexeNum.'DUAL' : 'A'.$anexeNum;
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
                    DigitalSignatureService::sign(
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
            } else {
                Alert::warning("No s'ha pogut descarregar el fitxer de la FCT Anexe II
                      $fctAl->idSao de $tmpFile");
            }
            $driver->get(self::HTTPS_FOREMP_EDU_GVA_ES_INDEX_PHP_OP_2_SUBOP_0);
            sleep(1);
        }
        return false;
    }
}
