<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\WebDriverBy;
use DB;
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

    public function birret(PasswordRequest $request)
    {
        try {
            $ss = new SeleniumService(authUser()->dni, $request->password);
            $ss->gTPersonalLlist();
        } catch (IntranetException $e) {
            Alert::danger($e->getMessage());
            return back();
        }


        $count = 0;
        $failures = 0;

        foreach (Falta_itaca::where('estado', 2)->whereMonth('dia', '=', $request->month)->get() as $falta) {
            try {
                $ss->fill(
                    WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'),
                    $falta->idProfesor
                );
                $ss->waitAndClick("//button[contains(text(),'Buscar')]");
                sleep(1);
                $element = $ss->getDriver()->findElement(
                    WebDriverBy::xpath("//div[contains(text(),'$falta->idProfesor')]"));
                $actions = new WebDriverActions($ss->getDriver());
                $actions->contextClick($element)->perform();
                $ss->waitAndClick("//span[contains(text(),'Faltas docente')]");
                sleep(3);
                $fechaActual = date('d/m/Y');
                $ss->fill(
                    WebDriverBy::xpath("//input[@value='$fechaActual']"),
                    $falta->dia
                );
                $ss->waitAndClick("//button[contains(text(),'Cambiar Fecha')]");
                $diaSemana = date('N', strtotime($falta->dia)) + 1;
                $hora = Hora::find($falta->sesion_orden);
                $textHora = $hora->hora_ini.' - '.$hora->hora_fin;
                $expresionXPath = "//table//tr/td[$diaSemana]//div[starts-with(@title, '$textHora')]";
                $ss->waitAndClick($expresionXPath);
                $ss->waitAndClick("//button[contains(text(),'Impartido por titular')]");
                sleep(1);

                $checkboxLabel = $ss->getDriver()->findElement(
                    WebDriverBy::xpath('//label[contains(text(), "Clase impartida por el profesor titular.")]'));
                $checkboxId = $checkboxLabel->getAttribute('for');
                $checkbox = $ss->getDriver()->findElement(WebDriverBy::id($checkboxId));
                if (!$checkbox->isSelected()) {
                    $checkbox->click();
                }
                $ss->waitAndClick("//button[contains(text(),'Guardar')]");
                $ss->waitAndClick("//button[contains(text(),'Aceptar')]");
                $ss->waitAndClick(WebDriverBy::className('z-icon-times'));
                $falta->estado = 4;
                $falta->save();
                $count++;
            } catch (\Exception $e) {
                Alert::danger($e->getMessage());
                try {
                    $ss->gTPersonalLlist();
                } catch (IntranetException $e) {
                    Alert::danger($e->getMessage());
                    $ss->quit();
                    Alert::info("$count faltas actualizadas, $failures errores");
                    return back();
                }
                $failures++;
            }
        }
        $ss->quit();
        Alert::info("$count faltas actualizadas, $failures errores");
        return back();
    }


    public function faltes(PasswordRequest $request)
    {
        try {
            $ss = new SeleniumService(authUser()->dni, $request->password);
            $ss->gTPersonalLlist();
        } catch (IntranetException $e) {
            Alert::danger($e->getMessage());
            return back();
        }


        $count = 0;
        $failures = 0;

        foreach (Falta::where('estado', 4)->where('dia_completo', 1)->whereMonth('hasta', '=', $request->month)->get() as $falta) {
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
                $desde= str_replace('-', '/', $falta->desde);
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
                    } else {
                        $ss->waitAndClick(WebDriverBy::xpath('//button[text()="Seleccionar todos"]'));
                        $ss->waitAndClick(WebDriverBy::xpath('//button[text()=" Nueva Falta"]'));
                        sleep(1);
                        $checkbox = $ss->getDriver()->findElement(WebDriverBy::cssSelector('input'));
                        if (!$checkbox->isSelected()) {
                            $checkbox->click();
                        }
                        $span = $ss->getDriver()->findElement(WebDriverBy::cssSelector('[data-id="cbJustificacion"]'));
                        $ss->fill(WebDriverBy::cssSelector('input.z-combobox-input'), $falta->motivo, $span);
                        $ss->waitAndClick(WebDriverBy::xpath('//button[data-tooltip="Guardar"]'));


                        //TODO: Falta actualizar
                    }
                }
            } catch (\Exception $e) {
                Alert::danger($e->getMessage());
                try {
                    $ss->gTPersonalLlist();
                } catch (IntranetException $e) {
                    Alert::danger($e->getMessage());
                    $ss->quit();
                    Alert::info("$count faltas actualizadas, $failures errores");
                    return back();
                }
                $failures++;
            }
        }
        $ss->quit();
        Alert::info("$count faltas actualizadas, $failures errores");
        return back();
    }
}


/*
            if ($falta->dia_completo) {
                //$this->waitAndClick(WebDriverBy::cssSelector('span[title="Listados de Faltas / Comunicados"]'));
                //$this->waitAndClick(WebDriverBy::cssSelector('[data-id="btnNuevi"]'));
                sleep(1);
                $divElement = $this->driver->findElement(WebDriverBy::xpath('//div[contains(text(), "Detalle faltas docente")]'));
                $modalElement = $divElement->findElement(WebDriverBy::xpath('..'));
                $desde= str_replace('-', '/', $falta->desde);
                $span = $modalElement->findElement(WebDriverBy::cssSelector('span.z-datebox[data-id="fechaInicialBaja"]'));
                $this->send(WebDriverBy::cssSelector('input.z-datebox-input'), $desde, $span);
                sleep(1);
                $hasta = str_replace('-', '/', $falta->hasta);
                $span1 = $modalElement->findElement(WebDriverBy::cssSelector('[data-id="fechaFinalBaja"]'));
                $this->send(WebDriverBy::cssSelector('input.z-datebox-input'), $hasta, $span1);
                $span2 = $modalElement->findElement(WebDriverBy::cssSelector('[data-id="justificada"]'));
                $checkbox = $span2->findElement(WebDriverBy::cssSelector('input'));
                if (!$checkbox->isSelected()) {
                    $checkbox->click();
                }
                $span = $modalElement->findElement(WebDriverBy::cssSelector('[data-id="cbJustificacion"]'));
                $this->send(WebDriverBy::cssSelector('input.z-combobox-input'), $falta->motivo, $span);
                dd('hola');
                $this->waitAndClick(WebDriverBy::xpath('//button[text()="Generar faltas"]'));
                $this->waitAndClick(WebDriverBy::xpath('//button[text()="Aceptar"]'));
                $this->waitAndClick(WebDriverBy::xpath('//i[@class="z-icon-times"]'));

            } else {

            }*/