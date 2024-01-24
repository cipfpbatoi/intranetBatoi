<?php

namespace Intranet\Sao;

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Intranet\Componentes\Mensaje;
use Intranet\Entities\Profesor;
use Intranet\Exceptions\CertException;
use Intranet\Exceptions\IntranetException;
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
        if ($file) {
            $file->move(dirname($nomFitxer), basename($nomFitxer));
        }

        try {
            if (isset($decrypt)) {
                $file = DigitalSignatureService::decryptCertificateUser($decrypt, authUser());
                $cert = DigitalSignatureService::readCertificat($nomFitxer, $passCert);
                self::downloadFilesFromFcts($driver, $fcts, $cert);
            } else {
                self::downloadFilesFromFcts($driver, $fcts);
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
            unlink($nomFitxer);
            $driver->quit();
            return back();
        }

        $driver->quit();
        return back();
    }


    public static function downloadFilesFromFcts(RemoteWebDriver $driver, $fcts, $certFile=null)
    {
        $tmpDirectory = config('variables.shareDirectory')??storage_path('tmp/');
        $signat = false;
        foreach ($fcts as $fct) {

            $fctAl = AlumnoFct::find($fct);
            if ($fctAl){
                // Anexe 1
                if ($fctAl->Fct->Colaboracion->Centro->Empresa->ConveniCaducat) {
                    try {
                        $idSao = $fctAl->Fct->Colaboracion->Centro->idSao;
                        $driver->get("https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc=101&centro=59&ct=$idSao");
                    } catch (\Throwable $exception) {
                        $tmpFile = $tmpDirectory."A1.pdf";
                        $saveFile = $fctAl->routeFile('1');
                        if (file_exists($tmpFile)) {
                            copy($tmpFile, $saveFile);
                        }
                        Firma::saveIfNotExists(1, $fctAl->idSao);
                        $signat = true;
                        unlink($tmpFile);
                    }
                    $driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
                    sleep(1);
                }
                // Anexe 2
                try {
                    $ad = "https://foremp.edu.gva.es/inc/ajax/generar_pdf.php".
                        "?doc=2&centro=59&idFct=$fctAl->idSao";
                    $driver->get($ad);
                } catch (\Throwable $exception) {
                    $tmpFile = $tmpDirectory."A2.pdf";
                    $saveFile = $fctAl->routeFile(2);
                    if (file_exists($tmpFile)) {
                        if ($certFile) {
                            $x = config("signatures.files.A2.owner.x");
                            $y = config("signatures.files.A2.owner.y");
                            DigitalSignatureService::sign(
                                $tmpFile,
                                $saveFile,
                                $x,
                                $y,
                                $certFile
                            );
                            Firma::saveIfNotExists(2, $fctAl->idSao, 1);
                        } else {
                            copy($tmpFile, $saveFile);
                            Firma::saveIfNotExists(2, $fctAl->idSao, 0);
                        }
                        $signat = true;
                        unlink($tmpFile);
                    } else {
                        Alert::warning("No s'ha pogut descarregar el fitxer de la FCT Anexe II
                      $fctAl->idSao de $tmpFile");
                    }
                    $driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
                    sleep(1);
                }
                // Anexe 3
                if ($certFile){
                    try {
                        $ad = "https://foremp.edu.gva.es/inc/ajax/generar_pdf.php".
                            "?doc=3&centro=59&idFct=$fctAl->idSao";
                        $driver->get($ad);
                    } catch (\Throwable $exception) {
                        $tmpFile = $tmpDirectory."A3.pdf";
                        $saveFile = $fctAl->routeFile(3);
                        if (file_exists($tmpFile)) {
                            $x = config("signatures.files.A3.owner.x");
                            $y = config("signatures.files.A3.owner.y");
                            DigitalSignatureService::sign(
                                $tmpFile,
                                $saveFile,
                                $x,
                                $y,
                                $certFile
                            );
                            Firma::saveIfNotExists(3, $fctAl->idSao, 1);
                            unlink($tmpFile);
                        } else {
                            Alert::warning("No s'ha pogut descarregar el fitxer de la FCT Anexe III
                         $fctAl->idSao de $tmpFile");
                        }
                        $driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
                        sleep(1);
                    }
                }
            }
        }
        if ($signat) {
            Mensaje::send(config('avisos.director'),'Tens nous documents per signar de '.authUser()->fullName);
        }
    }
}
