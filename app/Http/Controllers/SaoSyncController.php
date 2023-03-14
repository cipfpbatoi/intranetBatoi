<?php

namespace Intranet\Http\Controllers;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Services\SeleniumService;
use Intranet\Entities\AlumnoFctAval;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoSyncController
{

    public function index($password)
    {
        try {
            $driver = SeleniumService::loginSAO(AuthUser()->dni, $password);
            $alumnes = [];
            foreach (AlumnoFctAval::realFcts()->haEmpezado()->where('beca', 0)->activa()->get() as $fct) {
                try {
                    if ($fct->idSao) {
                        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=11&idFct=$fct->idSao");
                        sleep(1);
                        $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
                        $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                        $horas = explode(
                            '/',
                            $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText()
                        )[0];
                        if ($fct->realizadas != $horas) {
                            $fct->realizadas = $horas;
                            list($diarias,$ultima) =
                                $this->consultaDiario(
                                    $driver,
                                    $driver->findElement(WebDriverBy::cssSelector("#contenido"))
                                );
                            $fct->horas_diarias = $diarias;
                            $fct->actualizacion = fechaSao(substr($ultima, 2, 10));
                            $fct->save();
                            $alumnes[] = $fct->Alumno->shortName;
                        }
                    }
                } catch (NoSuchElementException $e) {
                    Alert::warning('No trobada informació '.$fct->Alumno->shortName);
                }
            }
            $this->alertSuccess($alumnes);
        } catch (Exception $e) {
            Alert::danger($e);
        }
        $driver->close();
        return back();
    }

    private function consultaDiario(RemoteWebDriver $driver, \Facebook\WebDriver\Remote\RemoteWebElement $contenido)
    {
        $find = false;
        $i=4;
        do {
            $a = $contenido->findElements(WebDriverBy::cssSelector("#texto_cont p.diasDelDiario a"));
            $hores = trim(
                $contenido->findElement(
                    WebDriverBy::
                    cssSelector("div#diario$i table.tablaDiario tbody tr:nth-child(2) td.celda1:nth-child(4)")
                )->getText());
            if ($hores > 0) {
                $find = true;
                $dia = explode(',', $a[$i]->getAttribute('href'))[2];
            }
        } while (!$find && $i-- >0);
        if ($find) {
            return array($hores, $dia);
        } else {
            $driver->findElement(WebDriverBy::cssSelector("p.celdaInfoAlumno a:nth-child(1)"))->click();
            sleep(1);
            return $this->consultaDiario($driver, $driver->findElement(WebDriverBy::cssSelector("#contenido")));
        }
    }

    protected function alertSuccess(array $alumnes, $message='Sincronitzades Fcts: ')
    {
        if (count($alumnes)) {
            $tots = '';
            foreach ($alumnes as $alumne) {
                $tots .= $alumne.', ';
            }
            Alert::info($message.$tots);
        }
    }

}
