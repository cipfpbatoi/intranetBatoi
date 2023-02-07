<?php

namespace Intranet\Http\Controllers;

use Exception;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Http\Request;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Services\AttachedFileService;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
trait traitSaoAnnexes
{

    public function annexes(Request $request)
    {
        $driver = RemoteWebDriver::create($this->serverUrl, DesiredCapabilities::firefox());
        try {
            $this->login($driver, trim($request->password));
            $alumnes = [];
            foreach (AlumnoFctAval::realFcts()->activa()->get() as $fct) {
                if ($fct->idSao) {
                    $driver->navigate()->to(
                        "https://foremp.edu.gva.es/inc/fcts/documentos_fct.php?id={$fct->idSao}&documento=2"
                    );
                    sleep(1);
                    try {
                        $name = trim(
                            $driver->findElement(
                                WebDriverBy::cssSelector("table.tablaListadoFCTs tbody tr:nth-child(2) td:nth-child(1)")
                            )->getText()
                        );
                        $onclick = $driver->findElement(
                            WebDriverBy::cssSelector(".botonSelec[value='Descargar']")
                        )->getAttribute('onclick');
                        $cut1 = explode("'", $onclick);
                        AttachedFileService::saveLink(
                            $name,
                            "https://foremp.edu.gva.es/".$cut1[1],
                            'SAO:Annexe II i III',
                            'zip',
                            "alumnofctaval/$fct->id"
                        );
                        $alumnes[] = $fct->Alumno->shortName;
                    } catch (Exception $e) {
                        Alert::info("Annexes de ".$fct->Alumno->fullName." no trobats");
                    }

                    $driver->findElement(WebDriverBy::cssSelector(".botonSelec[value='Cerrar']"))->click();
                    sleep(1);
                }
            }
            $this->alertSuccess($alumnes, 'Annexes Baixats: ');
        } catch (Exception $e) {
            Alert::danger($e);
        }
        $driver->close();
        return back();
    }

}
