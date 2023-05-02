<?php

namespace Intranet\Sao;

use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
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

        $caps = DesiredCapabilities::firefox();
        $caps->setCapability('firefox_profile', $profile);
        return $caps;
    }


    public static function index($driver, $request)
    {

        $driver->manage()->timeouts()->pageLoadTimeout(2);
        $fcts = array_keys($request, 'on');
        $decrypt = $request['decrypt']??null;
        $cert = $request['cert']??null;


        if (isset($decrypt)) {
            $nameFile = AuthUser()->fileName;
            $file = DigitalSignatureService::decryptCertificate($nameFile, $decrypt);
        }

        self::downloadFilesFromFcts($driver, $fcts, $file ?? null, $cert);

        if ($file) {
            unlink($file);
        }
        $driver->quit();
        return back();
    }


    public static function downloadFilesFromFcts(RemoteWebDriver $driver, $fcts, $certFile, $passCert)
    {
        $tmpDirectory = storage_path('tmp/');
        foreach ($fcts as $fct) {
                // Anexe 1
            $fctAl = AlumnoFct::find($fct);
            if (! $fctAl->Fct->Colaboracion->Centro->Empresa->ConveniNou) {
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
                    unlink($tmpFile);
                }
                $driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
                sleep(1);
            }
            for ($anexe = 2; $anexe <=3; $anexe++) {    // Anexe II
                try {
                    $ad = "https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc={$anexe}&centro=59&idFct=$fctAl->idSao";
                    $driver->get($ad);
                } catch (\Throwable $exception) {
                    $tmpFile = $tmpDirectory."A{$anexe}.pdf";
                    $saveFile = $fctAl->routeFile($anexe);
                    if (file_exists($tmpFile)) {
                        if ($certFile && $passCert) {
                            $x = config("signatures.files.A{$anexe}.owner.x");
                            $y = config("signatures.files.A{$anexe}.owner.y");
                            DigitalSignatureService::sign(
                                $tmpFile,
                                $saveFile,
                                $x,
                                $y,
                                $certFile,
                                $passCert
                            );
                        } else {
                            copy($tmpFile, $saveFile);
                        }
                        Firma::saveIfNotExists($anexe, $fctAl->idSao);
                        unlink($tmpFile);
                    } else {
                        Alert::warning("No s'ha pogut descarregar el fitxer de la FCT Anexe {$anexe} $fctAl->idSao");
                    }
                    $driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
                    sleep(1);
                }
            }
        }
    }
}
