<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use DB;
use Intranet\Exceptions\IntranetException;
use Styde\Html\Facades\Alert;
use Illuminate\Http\Request;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class ItacaController extends Controller
{

    protected $serverUrl;
    protected $driver;
    const WEB = 'https://acces.edu.gva.es/sso/login.xhtml';

    public function __construct()
    {
        //$this->serverUrl = env('SELENIUM_URL', 'http://172.16.9.10:4444');
        $this->serverUrl = env('SELENIUM_URL', 'http://192.168.56.1:4444');
        $this->driver = RemoteWebDriver::create($this->serverUrl, DesiredCapabilities::firefox());

        return parent::__construct();
    }


    public function post(Request $request)
    {
        $accion = $request->accion;
        return redirect()->route('sao.'.$accion, ['password' => $request->password]);
    }


    /**
     * @param  RemoteWebDriver  $driver
     * @return void
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    public function login($password='eiclmp5_A'): void
    {
        $this->driver->get($this::WEB);
        $dni = substr(AuthUser()->dni, -9);
        $this->driver->findElement(WebDriverBy::id('form1:j_username')) // find usuario
        ->sendKeys($dni);
        $this->driver->findElement(WebDriverBy::id('form1:j_password'))
            ->sendKeys($password);
        $this->driver->findElement(WebDriverBy::name('form1:j_id47'))
            ->click();
        sleep(1);
        $this->driver->get('https://docent.edu.gva.es/md-front/www/#moduldocent/centres');
        sleep(3);
        $this->driver->get('https://docent.edu.gva.es/md-front/www/#centre/03012165/horari');
        sleep(5);
        //$name = $driver->findElement(WebDriverBy::cssSelector('.botonform'))->getAttribute('name');
        //if ($name === 'login') {
        //    throw new IntranetException('Password no vàlid. Has de ficarl el del SAO');
        //}
        $this->driver->close();
    }


}
