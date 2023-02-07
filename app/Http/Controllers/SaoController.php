<?php

namespace Intranet\Http\Controllers;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use DB;
use Intranet\Entities\Profesor;
use Intranet\Exceptions\IntranetException;
use Styde\Html\Facades\Alert;
use Illuminate\Http\Request;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoController extends Controller
{
    use traitSaoAnnexes,traitSaoSync,traitSaoImporta,traitSaoCompara;

    const TD_NTH_CHILD_2 = "td:nth-child(2)";
    const TR_NTH_CHILD_2 = "tr:nth-child(2)";
    const TD_NTH_CHILD_3 = "td:nth-child(3)";
    const TD_NTH_CHILD_4 = "td:nth-child(4)";
    protected $serverUrl;
    const WEB = 'https://foremp.edu.gva.es/index.php';

    public function __construct()
    {
        $this->serverUrl = env('SELENIUM_URL', 'http://172.16.9.10:4444');
        //$this->serverUrl = env('SELENIUM_URL', 'http://192.168.56.1:4444');

        return parent::__construct();
    }
    public function index()
    {
        $tutores = Profesor::tutoresFCT()->orderBy('apellido1')->orderBy('apellido2')->get();
        $action = 'download';
        return view('sao.index', compact('tutores', 'action'));
    }

    public function post(Request $request)
    {
        $accion = $request->accion;
        return $this->$accion($request);
    }

    public function accion(Request $request, $accion)
    {
        return $this->$accion($request);
    }

    protected function alertSuccess(array $alumnes, $message='Sincronitzades Fcts: ')
    {
        if (count($alumnes)) {
            $tots = '';
            foreach ($alumnes as $alumne) {
                $tots .= $alumne.', ';
            }
            Alert::info($message.$tots);
        }
    }


    /**
     * @param  RemoteWebDriver  $driver
     * @return void
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    private function login(RemoteWebDriver $driver, $password): void
    {
        $driver->get($this::WEB);
        $dni = substr(AuthUser()->dni, -9);
        $driver->findElement(WebDriverBy::name('usuario')) // find usuario
        ->sendKeys($dni);
        $driver->findElement(WebDriverBy::name('password'))
            ->sendKeys($password);
        $driver->findElement(WebDriverBy::cssSelector('.botonform'))
            ->click();
        $driver->get('https://foremp.edu.gva.es/index.php?op=2&subop=0');
        sleep(1);
        $name = $driver->findElement(WebDriverBy::cssSelector('.botonform'))->getAttribute('name');
        if ($name === 'login') {
            throw new IntranetException('Password no vàlid. Has de ficarl el del SAO');
        }
    }


}
