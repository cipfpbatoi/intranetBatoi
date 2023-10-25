<?php

namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Entities\AlumnoFctAval;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class Sync
{

    public static function index($driver)
    {
        try {
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
                            $fct->realizadas = (int) $horas;
                            try {
                                list($diarias,$ultima) =
                                    self::consultaDiario(
                                        $driver,
                                        $driver->findElement(WebDriverBy::cssSelector("#contenido"))
                                    );
                                $fct->horas_diarias = (float)$diarias;
                                $fct->actualizacion = fechaSao(substr($ultima, 2, 10));
                            } catch (\Exception $e){
                                Alert::info('Informació incompleta '.$fct->Alumno->shortName);
                            }
                            $fct->save();
                            $alumnes[] = $fct->Alumno->shortName;
                        }
                    }
                } catch (NoSuchElementException $e) {
                    Alert::warning('No trobada informació '.$fct->Alumno->shortName);
                }
            }
            arrayAlert($alumnes);
        } catch (Exception $e) {
            Alert::danger($e);
        }
        if (isset($driver)) {
            $driver->quit();
        }
        return back();
    }

    private static function consultaDiario(RemoteWebDriver $driver, \Facebook\WebDriver\Remote\RemoteWebElement $contenido)
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
            return self::consultaDiario($driver, $driver->findElement(WebDriverBy::cssSelector("#contenido")));
        }
    }



}
