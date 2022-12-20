<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Centro;
use Intranet\Entities\Grupo;
use Intranet\Entities\Fct;
use DB;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Instructor;
use function PHPUnit\Framework\throwException;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class ItacaController extends Controller
{
    const SERVER_URL = 'http://192.168.56.1:4444';
    const WEB = 'https://acces.edu.gva.es/sso/login.xhtml?callbackUrl=https://acces.edu.gva.es/escriptori/';
    const WEB_AFTER_LOGIN = 'https://docent.edu.gva.es/md-front/www/#moduldocent/centres';

    public function login(){
        $driver = RemoteWebDriver::create($this::SERVER_URL, DesiredCapabilities::firefox());
        try {
            $driver->get($this::WEB);
            $dni = substr(AuthUser()->dni,-9);
            $password = 'EICLMP5_a';
            $driver->findElement(WebDriverBy::name("form1:j_username")) // find usuario
            ->sendKeys($dni);
            $driver->findElement(WebDriverBy::name('form1:j_password'))
                ->sendKeys($password);
            $driver->findElement(WebDriverBy::name("form1:j_id39"))
                ->click();
            sleep(1);
            $driver->get($this::WEB_AFTER_LOGIN);
            sleep(2);
        } catch (\Exception $e){
            echo $e->getMessage();
        }
        $driver->close();
    }
}


