<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use DB;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverWait;
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
        $driver = SeleniumService::loginItaca();
        $driver->get('https://itaca3.edu.gva.es/itaca3-gad/');
        sleep(2);
        $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Gestión')]"))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Personal')]"))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Listado Personal')]"))->click();
        sleep(1);
        $formulari = $driver->findElement(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'));
        $formulari->sendKeys('021652470V');
        $driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Buscar')]"))->click();
        $element = $driver->findElement(WebDriverBy::xpath("//div[contains(text(),'021652470V')]"));
        $actions = new WebDriverActions($driver);
        $actions->contextClick($element)->perform();
        sleep(3);

        $driver->findElement(WebDriverBy::xpath("//span[contains(text(),'Faltas docente')]"))->click();

        sleep(5);
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
