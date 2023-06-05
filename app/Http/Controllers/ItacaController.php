<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\WebDriverBy;
use DB;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Intranet\Entities\Falta_itaca;
use Intranet\Entities\Hora;
use Intranet\Services\SeleniumService;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class ItacaController extends Controller
{
    private function goToLlist($driver)
    {
        $driver->get('https://itaca3.edu.gva.es/itaca3-gad/');
        sleep(2);
        $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Gestión')]"))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Personal')]"))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Listado Personal')]"))->click();
        sleep(1);
        return $driver;
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function birret()
    {
        $driver = SeleniumService::loginItaca();
        $driver = $this->goToLlist($driver);
        $count = 0;
        $failures = 0;

        foreach (Falta_itaca::where('estado', 2)->where('dia', '>=', '2023/05/01')->get() as $falta) {
            try {
                $formulari = $driver->findElement(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'));
                $formulari->clear();
                $formulari->sendKeys($falta->idProfesor);
                sleep(0.5);
                $driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Buscar')]"))->click();
                sleep(1);
                $element = $driver->findElement(WebDriverBy::xpath("//div[contains(text(),'$falta->idProfesor')]"));
                $actions = new WebDriverActions($driver);
                $actions->contextClick($element)->perform();
                $wait = new WebDriverWait($driver, 10);
                $menuContextual = WebDriverBy::xpath("//span[contains(text(),'Faltas docente')]");
                $wait->until(WebDriverExpectedCondition::elementToBeClickable($menuContextual));
                $driver->findElement($menuContextual)->click();
                sleep(3);
                $fechaActual = date('d/m/Y');
                $formulari = $driver->findElement(WebDriverBy::xpath("//input[@value='$fechaActual']"));
                $formulari->clear();
                $formulari->sendKeys($falta->dia);
                sleep(0.5);
                $driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Cambiar Fecha')]"))->click();
                sleep(1);
                $dia_semana = date('N', strtotime($falta->dia)) + 1;
                $hora = Hora::find($falta->sesion_orden);
                $textHora = $hora->hora_ini.' - '.$hora->hora_fin;
                $expresionXPath = "//table//tr/td[$dia_semana]//div[starts-with(@title, '$textHora')]";
                $driver->findElement(WebDriverBy::xpath($expresionXPath))->click();
                sleep(1);
                $driver->findElement(
                    WebDriverBy::xpath("//button[contains(text(),'Impartido por titular')]"))->click();
                sleep(1);

                $checkboxLabel = $driver->findElement(
                    WebDriverBy::xpath('//label[contains(text(), "Clase impartida por el profesor titular.")]'));
                $checkboxId = $checkboxLabel->getAttribute('for');
                $checkbox = $driver->findElement(WebDriverBy::id($checkboxId));
                if (!$checkbox->isSelected()) {
                    $checkbox->click();
                    sleep(1);
                }
                $button = $driver->findElement(WebDriverBy::xpath('//button[contains(text(), "Guardar")]'));
                $button->click();
                sleep(1);
                $button = $driver->findElement(WebDriverBy::xpath('//button[contains(text(), "Aceptar")]'));
                $button->click();
                sleep(1);
                $modalLocator = WebDriverBy::className('z-icon-times');
                $driver->findElement($modalLocator)->click();
                $falta->estado = 4;
                $falta->save();
                $count++;
            } catch (\Exception $e) {
                Alert::danger($e->getMessage());
                $driver = $this->goToLlist($driver);
                $failures++;
            }
        }
        $driver->quit();
        Alert::info("$count faltas actualizadas, $failures errores");
        return back();
    }
}
