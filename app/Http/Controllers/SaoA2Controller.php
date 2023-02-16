<?php

namespace Intranet\Http\Controllers;

use Exception;
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


    public function download_file_from_fcts($password)
    {
        $fctAl = AlumnoFct::misFcts()->activa()->first();

        foreach (AlumnoFct::misFcts()->activa()->get() as $fctAl) {
            try {
                $driver = RemoteWebDriver::create($this->serverUrl, DesiredCapabilities::firefox());
                $this->login($driver, $password);
                $driver->get("https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc=2&centro=59&idFct=$fctAl->idSao");
                sleep(1);
                $driver->close();
            } catch (Exception $exception) {
                Alert::info($exception->getMessage());
            }
        }
    }

    public function index($password)
    {
        $grupo = Grupo::where('tutor', AuthUser()->dni)->first();
        $ciclo = $grupo->idCiclo??null;

        if (!$ciclo) {
            Alert::danger('No eres tutor');
            return redirect(route('alumnofct.index'));
        } else {
            try {
                $this->download_file_from_fcts($password);
            } catch (Exception $e) {
                Alert::warning($e->getMessage());
                return redirect(route('alumnofct.index'));
            }
        }
    }

}
