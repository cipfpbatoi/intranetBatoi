<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use DB;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\SeleniumService;
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

    }




        $this->driver->get('https://docent.edu.gva.es/md-front/www/#moduldocent/centres');
        sleep(3);
        $this->driver->get('https://docent.edu.gva.es/md-front/www/#centre/03012165/horari');
        sleep(3);
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
    }


}
