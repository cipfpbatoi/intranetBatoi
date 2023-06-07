<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\WebDriverBy;
use DB;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Http\Request;
use Intranet\Entities\Falta_itaca;
use Intranet\Entities\Hora;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\SeleniumService;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class ItacaController extends Controller
{
    private $driver;

    private function waitAndClick($xpath)
    {
        if (is_string($xpath)) {
            $element = WebDriverBy::xpath($xpath);
        } else {
            $element = $xpath;
        }
        $wait = new WebDriverWait($this->driver, 10);
        $wait->until(WebDriverExpectedCondition::elementToBeClickable($element));
        $this->driver->findElement($element)->click();
    }

    private function send($selector, $keys)
    {
        $formulari = $this->driver->findElement($selector);
        $formulari->clear();
        $formulari->sendKeys($keys);
    }


    private function goToLlist()
    {
        try {
            $this->driver->get('https://itaca3.edu.gva.es/itaca3-gad/');
            $this->waitAndClick("//span[contains(text(),'Gestión')]");
            $this->waitAndClick("//span[contains(text(),'Personal')]");
            $this->waitAndClick("//span[contains(text(),'Listado Personal')]");
        } catch (\Exception $e) {
            $this->driver->quit();
            throw new IntranetException($e->getMessage());
        }
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function birret(Request $request)
    {
        try {
            $dni = authUser()->dni;
            $this->driver = SeleniumService::loginItaca($dni, $request->password);
            $this->goToLlist();
        } catch (IntranetException $e) {
            Alert::danger($e->getMessage());
            return back();
        }


        $count = 0;
        $failures = 0;

        foreach (Falta_itaca::where('estado', 2)->whereMonth('dia', '=', $request->month)->get() as $falta) {
            try {
                $this->send(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'), $falta->idProfesor);
                $this->waitAndClick("//button[contains(text(),'Buscar')]");
                sleep(1);
                $element = $this->driver->findElement(
                    WebDriverBy::xpath("//div[contains(text(),'$falta->idProfesor')]"));
                $actions = new WebDriverActions($this->driver);
                $actions->contextClick($element)->perform();
                $this->waitAndClick("//span[contains(text(),'Faltas docente')]");
                sleep(3);
                $fechaActual = date('d/m/Y');
                $this->send(WebDriverBy::xpath("//input[@value='$fechaActual']"), $falta->dia);
                $this->waitAndClick("//button[contains(text(),'Cambiar Fecha')]");
                $diaSemana = date('N', strtotime($falta->dia)) + 1;
                $hora = Hora::find($falta->sesion_orden);
                $textHora = $hora->hora_ini.' - '.$hora->hora_fin;
                $expresionXPath = "//table//tr/td[$diaSemana]//div[starts-with(@title, '$textHora')]";
                $this->waitAndClick($expresionXPath);
                $this->waitAndClick("//button[contains(text(),'Impartido por titular')]");
                sleep(1);

                $checkboxLabel = $this->driver->findElement(
                    WebDriverBy::xpath('//label[contains(text(), "Clase impartida por el profesor titular.")]'));
                $checkboxId = $checkboxLabel->getAttribute('for');
                $checkbox = $this->driver->findElement(WebDriverBy::id($checkboxId));
                if (!$checkbox->isSelected()) {
                    $checkbox->click();
                }
                $this->waitAndClick("//button[contains(text(),'Guardar')]");
                $this->waitAndClick("//button[contains(text(),'Aceptar')]");
                $this->waitAndClick(WebDriverBy::className('z-icon-times'));
                $falta->estado = 4;
                $falta->save();
                $count++;
            } catch (\Exception $e) {
                Alert::danger($e->getMessage());
                try {
                    $this->goToLlist();
                } catch (IntranetException $e) {
                    Alert::danger($e->getMessage());
                    $this->driver->quit();
                    Alert::info("$count faltas actualizadas, $failures errores");
                    return back();
                }
                $failures++;
            }
        }
        $this->driver->quit();
        Alert::info("$count faltas actualizadas, $failures errores");
        return back();
    }
}
