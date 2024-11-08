<?php

namespace Intranet\Http\Controllers;


use Carbon\Carbon;
use DateTime;
use Facebook\WebDriver\WebDriverBy;
use DB;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Http\Request;
use Intranet\Entities\Falta;
use Intranet\Entities\Falta_itaca;
use Intranet\Entities\Hora;
use Intranet\Exceptions\IntranetException;
use Intranet\Http\Requests\PasswordRequest;
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
    private function closeNoticias()
    {
        try {
            $remainingWindows = []; // Lista para las ventanas pendientes de cerrar
            $retryCount = 0; // Contador de intentos para evitar bucles infinitos

            while (true) {
                // Encuentra todos los botones de cierre de ventanas
                $elements = $this->driver->findElements(WebDriverBy::cssSelector('.z-window-close.imc--bt-terciari'));

                // Si no hay más ventanas y no quedan pendientes, rompe el bucle
                if (empty($elements) && empty($remainingWindows)) {
                    break;
                }

                // Intenta cerrar cada ventana en el array `elements`
                foreach ($elements as $element) {
                    try {
                        $element->click();
                        usleep(500000); // Pausa breve para permitir que la ventana se cierre
                    } catch (\Exception $e) {
                        // Si no puede cerrarse, la añade a pendientes
                        $remainingWindows[] = $element;
                    }
                }

                // Procesa las ventanas pendientes
                $elements = $remainingWindows;
                $remainingWindows = []; // Resetea la lista de pendientes

                // Incrementa el contador de intentos
                $retryCount++;

                // Si hemos intentado demasiadas veces, salimos del bucle para evitar un bucle infinito
                if ($retryCount > 10) {
                    break;
                }
            }
        } catch (\Exception $e) {
            // No pasa nada si ocurre una excepción
        }
    }


    /*private function closeNoticias()
    {
        try {
            $elements = $this->driver->findElements(WebDriverBy::cssSelector('.z-window-close.imc--bt-terciari'));
            foreach ($elements??[] as $element) {
                $element->click();
            }
        } catch (\Exception $e) {
            // No pasa res
        }
    }*/


    private function goToLlist()
    {
        try {
            $this->driver->get('https://itaca3.edu.gva.es/itaca3-gad/');
            $this->closeNoticias();
            $this->waitAndClick("//span[contains(text(),'Gestión')]");
            $this->waitAndClick("//span[contains(text(),'Personal')]");
            $this->driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Listado Personal')]"))->click();
        } catch (\Exception $e) {
        }
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function birret(Request $request)
    {
        $faltas = Falta_itaca::where('estado', 2)->whereMonth('dia', '=', $request->month)->get();
        $total = count($faltas);
        if ($total == 0) {
            Alert::info('No hi ha faltas pendents');
            return back();
        }
        try {
            $dni = authUser()->dni;
            $this->driver = SeleniumService::loginItaca($dni, $request->password);
            $this->goToLlist();
        } catch (IntranetException $e) {
            Alert::danger('No he pogut loguejar-me:'. $e->getMessage());
            return back();
        }


        $count = 0;
        $failures = 0;


        foreach ($faltas as $falta) {
            try {
                $this->send(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'), $falta->idProfesor);
                $this->waitAndClick("//button[contains(text(),'Buscar')]");
                sleep(1);
                $element = $this->driver->findElement(
                    WebDriverBy::xpath("//div[contains(text(),'$falta->idProfesor')]"));
                $actions = new WebDriverActions($this->driver);
                $actions->contextClick($element)->perform();
                $this->waitAndClick("//span[contains(text(),'Faltas docente')]");
                sleep(1);
                $fechaActual = date('d/m/Y');
                $this->send(WebDriverBy::xpath("//input[@value='$fechaActual']"), $falta->dia);
                $this->waitAndClick("//button[contains(text(),'Cambiar Fecha')]");
                sleep(2);
                $diaSemana = date('N', strtotime($falta->dia)) + 1;
                $hora = Hora::find($falta->sesion_orden);
                $textHora = $hora->hora_ini.' - '.$hora->hora_fin;
                $expresionXPath = "//table//tr/td[$diaSemana]//div[starts-with(@title, '$textHora')]";
                $this->waitAndClick($expresionXPath);
                sleep(1);
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
                Alert::danger(
                    $e->getMessage().' '.$falta->Profesor->shortName.' '.$falta->dia.' '.$falta->sesion_orden
                );
                try {
                    $this->goToLlist();
                } catch (IntranetException $e) {
                    Alert::danger($e->getMessage());
                    $this->driver->quit();
                    Alert::info("$count faltas actualizadas, $failures errores de $total");
                    return back();
                }
                $failures++;
            }
        }
        $this->driver->quit();
        Alert::info("$count faltas actualizadas, $failures errores de $total");
        return back();
    }

    public function faltes(PasswordRequest $request)
    {
        try {
            $ss = new SeleniumService(authUser()->dni, $request->password);
        } catch (IntranetException $e) {
            Alert::danger($e->getMessage());
            return back();
        }


        $count = 0;
        $failures = 0;

        try {
            foreach (Falta::
            where('estado', 3)->
            where('itaca', 0)->
            where('dia_completo', 1)->
            whereColumn('desde', 'hasta')->
            whereMonth('hasta', '=', $request->month)
                         ->get() as $falta) {
                $fecha = Carbon::parse($falta->desde)->format('d-m-Y');
                $ss->gTPersonalLlist();
                if ($this->tryOne($ss, $falta,$fecha)) {
                    $count++;
                } else {
                    $failures++;
                }
            }
            foreach (Falta::
            where('estado', 3)->
            where('itaca', 0)->
            where('dia_completo', 1)->
            whereRaw('DATEDIFF(hasta, desde) BETWEEN 1 AND 3')->
            whereMonth('hasta', '=', $request->month)
                         ->get() as $falta) {
                $desde = Carbon::parse($falta->desde);
                $hasta = Carbon::parse($falta->hasta);

                // Calcula la diferència en dies
                $diferenciaDias = $desde->diffInDays($hasta);
                for ($i = 0; $i <= $diferenciaDias; $i++) {
                    $fecha = $desde->addDays($i)->format('d-m-Y');
                    $ss->gTPersonalLlist();
                    if ($this->tryOne($ss, $falta,$fecha)) {
                        $count++;
                    } else {
                        $failures++;
                    }
                }
                $falta->itaca = 1;
                $falta->save();
            }
        } catch (IntranetException $e) {
            Alert::danger($e->getMessage());
        }
        if ($ss->getDriver()) {
            $ss->quit();
        }
        Alert::info("$count faltas actualizadas, $failures errores");
        return back();
    }

    /**
     * @param  SeleniumService  $ss
     * @param  mixed  $falta
     * @param  int  $failures
     * @return int
     * @throws IntranetException
     */
    private function tryOne(SeleniumService $ss, mixed $falta, $fecha): int
    {
        try {
            sleep(1);
            $ss->fill(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'), $falta->idProfesor);
            $ss->waitAndClick("//button[contains(text(),'Buscar')]");
            sleep(2);
            $element = $ss->getDriver()->findElement(
                WebDriverBy::xpath("//div[contains(text(),'$falta->idProfesor')]"));
            $actions = new WebDriverActions($ss->getDriver());
            $actions->contextClick($element)->perform();
            $ss->waitAndClick("//span[contains(text(),'Faltas docente')]");
            $desde = str_replace('-', '/', $fecha);
            sleep(1);
            $ss->fill(WebDriverBy::cssSelector('input.z-datebox-input'), $desde);
            $ss->waitAndClick(WebDriverBy::xpath('//button[text()="Cambiar Fecha"]'));
            sleep(1);
            if ($falta->dia_completo) {
                $ss->waitAndClick(WebDriverBy::xpath('//button[text()="Vista diaria"]'));
                sleep(1);
                $elemento = $ss->getDriver()->findElement(WebDriverBy::cssSelector('.z-calevent-t1'));
                $atributoStyle = $elemento->getAttribute('style');
                $colorFondo = substr($atributoStyle, strpos($atributoStyle, '#') + 1);
                if ($colorFondo === 'ff5d00') {
                    $ss->waitAndClick(WebDriverBy::className('z-icon-times'));
                    Alert::info('Falta ja actualizada');
                    $falta->itaca = true;
                    $falta->save();
                } else {
                    $ss->waitAndClick(WebDriverBy::xpath('//button[text()="Seleccionar todos"]'));
                    $ss->waitAndClick(WebDriverBy::xpath('//button[text()=" Nueva Falta"]'));
                     sleep(2);
                    $selector = "span.z-checkbox[data-id='justificada'] input";
                    $element = $ss->getDriver()->findElement(WebDriverBy::cssSelector($selector));
                    $checkbox = $ss
                        ->getDriver()
                        ->findElement(WebDriverBy::cssSelector($selector));
                    if (!$checkbox->isSelected()) {
                        $checkbox->click();
                    }
                    $selector = "span.z-combobox[data-id='cbJustificacion'] input";
                    $input = $ss->getDriver()
                        ->findElement(WebDriverBy::cssSelector($selector));
                    $input->sendKeys($falta->motivo);
                    $button = $ss->getDriver()->findElement(WebDriverBy::cssSelector("button.z-button[data-tooltip='Guardar']"));
                    $button->click();
                    Alert::info('Falta actualizada');
                    $falta->itaca = true;
                    $falta->save();
                }
                return 1;
            }
        } catch (\Exception $e) {
            Alert::danger($e->getMessage());
            return 0;
        }
        return 1;
    }
}
