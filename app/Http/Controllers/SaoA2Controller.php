<?php

namespace Intranet\Http\Controllers;

use Exception;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Grupo;
use Intranet\Services\DigitalSignatureService;
use Intranet\Services\SeleniumService;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoA2Controller extends Controller
{

    public function index($password)
    {
        $grupo = Grupo::where('tutor', AuthUser()->dni)->first();
        $ciclo = $grupo->idCiclo??null;

        if (!$ciclo) {
            Alert::danger('No eres tutor');
            return redirect(route('alumnofct.index'));
        } else {
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
            $driver = SeleniumService::loginSAO(AuthUser()->dni, $password, $caps);
            $driver->manage()->timeouts()->pageLoadTimeout(2);
            try {
                $nameFile = AuthUser()->fileName;
                $file = DigitalSignatureService::decryptCertificate($nameFile, 'EICLMP5_a');
                $this->download_file_from_fcts($driver, 2, $file);
                $this->download_file_from_fcts($driver, 3, $file);
                unlink($file);
                $driver->close();
            } catch (Exception $e) {
                Alert::warning($e->getMessage());
                $driver->close();
            }
        }
        return redirect(route('alumnofct.index'));
    }

    public function download_file_from_fcts(RemoteWebDriver $driver, $anexe, $certFile, $passCert = 'EICLMP5_a')
    {
        $copyDirectory = storage_path('app/annexes/');
        $tmpDirectory = storage_path('tmp/');
        foreach (AlumnoFct::misFcts()->activa()->get() as $fctAl) {
            try {
                $driver->get("https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc={$anexe}&centro=59&idFct=$fctAl->idSao");
            } catch (\Throwable $exception) {
                $tmpFile = $tmpDirectory."A{$anexe}.pdf";
                $saveFile = $copyDirectory."A{$anexe}_$fctAl->idSao.pdf";
                $x = config("signatures.files.A{$anexe}.owner.x");
                $y = config("signatures.files.A{$anexe}.owner.y");
                if (file_exists($tmpFile)) {
                    DigitalSignatureService::sign(
                        $tmpFile,
                        $saveFile,
                        $x,
                        $y,
                        $certFile,
                        $passCert
                    );
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
