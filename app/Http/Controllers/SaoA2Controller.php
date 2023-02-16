<?php

namespace Intranet\Http\Controllers;

use Exception;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Http\Request;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Centro;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;
use Intranet\Entities\Grupo;
use Intranet\Entities\Empresa;
use Intranet\Entities\Instructor;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoA2Controller extends SaoController
{


    public function download_file_from_fcts($driver)
    {
        $fctAl = AlumnoFct::misFcts()->activa()->first();

        foreach (AlumnoFct::misFcts()->activa()->get() as $fctAl) {
            try {
                $driver->get("https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc=2&centro=59&idFct=$fctAl->idSao");
                sleep(1);
                //$driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
                //sleep(1);
            } catch (\Throwable $exception) {
                $driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
                sleep(1);
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
            $driver = RemoteWebDriver::create($this->serverUrl, DesiredCapabilities::firefox());
            try {
                $this->login($driver, $password);
                $this->download_file_from_fcts($driver);
                $driver->close();
            } catch (Exception $e) {
                Alert::warning($e->getMessage());
                $driver->close();
                return redirect(route('alumnofct.index'));
            }
        }
    }

}
