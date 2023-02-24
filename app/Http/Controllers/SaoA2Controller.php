<?php

namespace Intranet\Http\Controllers;

use Exception;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Grupo;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoA2Controller extends SaoController
{

    public function index($password)
    {
        $grupo = Grupo::where('tutor', AuthUser()->dni)->first();
        $ciclo = $grupo->idCiclo??null;

        $profile = new FirefoxProfile();
        $caps = DesiredCapabilities::firefox();

        $profile->setPreference('browser.download.folderList', 2);
        $profile->setPreference('browser.download.dir', __DIR__.'/Downloads');
        $profile->setPreference('browser.helperApps.neverAsk.saveToDisk', 'application/pdf');
        $profile->setPreference('pdfjs.enabledCache.state', false);
        $profile->setPreference('modifyheaders.headers.count', 1);
        $profile->setPreference("modifyheaders.headers.action0", "Add");
        $profile->setPreference("modifyheaders.headers.name0", "Content-Disposition"); # Set here the name of the header
        $profile->setPreference("modifyheaders.headers.value0", "inline"); # Set here the value of the header
        $profile->setPreference("modifyheaders.headers.enabled0", true);
        $profile->setPreference("modifyheaders.config.active", true);
        $profile->setPreference("modifyheaders.config.alwaysOn", true);
        $caps->setCapability('firefox_profile', $profile);


        if (!$ciclo) {
            Alert::danger('No eres tutor');
            return redirect(route('alumnofct.index'));
        } else {
            $driver = RemoteWebDriver::create($this->serverUrl, $caps);
            $driver->manage()->timeouts()->pageLoadTimeout(2);
            try {
                $this->login($driver, $password);
                $this->download_file_from_fcts($driver);
                $driver->close();
            } catch (Exception $e) {
                Alert::warning($e->getMessage());
                $driver->close();
            }
        }
        return redirect(route('alumnofct.index'));
    }

}
