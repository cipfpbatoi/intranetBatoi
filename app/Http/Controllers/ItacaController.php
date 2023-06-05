<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use DB;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverWait;
use Intranet\Entities\Falta_itaca;
use Intranet\Entities\Hora;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\SeleniumService;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Styde\Html\Facades\Alert;
use Illuminate\Http\Request;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class ItacaController extends Controller
{

    public function index()
    {
        /*foreach (Falta_itaca::where('estado', 2)->where('dia', '>', '2023/03/01')->get() as $falta) {
            $hora = Hora::find($falta->sesion_orden);
            dd($hora->hora_ini.' - '.$hora->hora_fin);
        }*/
        $driver = SeleniumService::loginItaca();
        $driver->get('https://itaca3.edu.gva.es/itaca3-gad/');
        sleep(2);
        foreach (Falta_itaca::where('estado', 1)->where('dia', '>', '2023/05/01')->get() as $falta) {
            $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Gestión')]"))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Personal')]"))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Listado Personal')]"))->click();
            sleep(1);
            $formulari = $driver->findElement(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'));
            $formulari->sendKeys($falta->idProfesor);
            $driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Buscar')]"))->click();
            $element = $driver->findElement(WebDriverBy::xpath("//div[contains(text(),'$falta->idProfesor')]"));
            $actions = new WebDriverActions($driver);
            $actions->contextClick($element)->perform();
            sleep(3);
            $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Faltas docente')]"))->click();
            sleep(3);
            $fechaActual = date('d/m/Y');
            $formulari = $driver->findElement(WebDriverBy::xpath("//input[@value='$fechaActual']"));
            $formulari->clear();
            $formulari->sendKeys($falta->dia);
            sleep(0.5);
            $driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Cambiar Fecha')]"))->click();
            sleep(1);
            $dia_semana = date('N', strtotime($falta->dia))+1;
            $hora = Hora::find($falta->sesion_orden);
            $textHora = $hora->hora_ini.' - '.$hora->hora_fin;
            $expresionXPath = "//table//tr/td[$dia_semana]//div[starts-with(@title, '$textHora')]";
            $driver->findElement(WebDriverBy::xpath($expresionXPath))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Impartido por titular')]"))->click();
            sleep(1);
            $checkboxLabel = $driver->findElement(WebDriverBy::xpath('//label[contains(text(), "Clase impartida por el profesor titular.")]'));
            $checkboxId = $checkboxLabel->getAttribute('for');
            $checkbox = $driver->findElement(WebDriverBy::id($checkboxId));
            $checkbox->click();
            sleep(1);
            $button = $driver->findElement(WebDriverBy::xpath('//button[contains(text(), "Guardar")]'));
            $button->click();
            sleep(1);
            $button = $driver->findElement(WebDriverBy::xpath('//button[contains(text(), "Aceptar")]'));
            $button->click();
            sleep(1);
        }

        $driver->close();
        /*
        $ul = $this->driver->findElement(
                WebDriverBy::cssSelector('ul.imc-horari-dies li.imc-horari-dia:nth-child(1)')
            );
        $data = $ul->findElement(WebDriverBy::cssSelector('h2.imc-dia'))->getAttribute('data-data');
        if ($data == date('Y-m-d')) {
            $inici = $ul->findElement(WebDriverBy::cssSelector('ul.imc-horari-sessions'));
            $dies = $inici->findElements(WebDriverBy::cssSelector('li'));
            foreach ($dies as $dia) {
                $grupsid = $dia->getAttribute('data-grupsid');
                $horari = $dia->getAttribute('data-horari');
                $sessio = $dia->getAttribute('data-sessio');
                $desde = $dia->getAttribute('data-desde');
                $link = "https://docent.edu.gva.es/md-front/www/#centre/03012165/grup/{$grupsid},/tasques/diaries/perSessio/sessio/{$sessio};{$horari},;{$data};{$desde}/desdeHorari";
                dd($link);
                $this->driver->get($link);
                sleep(1);
                //https://docent.edu.gva.es/md-front/www/#centre/03012165/grup/2937520721,/tasques/diaries/perSessio/sessio/1165779796;1165782976,;2023-03-03;11:00/desdeHorari
            }

        } else {
            Alert::info('No hay horario para hoy');
        }


        $this->driver->close();
        */
    }


}
