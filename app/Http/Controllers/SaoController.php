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
use Intranet\Entities\Profesor;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Empresa;
use Illuminate\Http\Request;
use Exception;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoController extends Controller
{
    const SERVER_URL = 'http://192.168.56.1:4444';
    const WEB = 'https://foremp.edu.gva.es/index.php';

    public function index(){
        $tutores = Profesor::tutoresFCT()->orderBy('apellido1')->orderBy('apellido2')->get();
        $action = 'download';
        return view('sao.index',compact('tutores','action'));
    }


    public function sync(Request $request){
        $dni = $request->profesor;
        $driver = RemoteWebDriver::create($this::SERVER_URL, DesiredCapabilities::firefox());
        try {
            $this->login($driver, trim($request->password));
            foreach (AlumnoFct::misFcts()->activa()->get() as $fct){
                if ($fct->idSao){
                    $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=11&idFct=$fct->idSao");
                    sleep(1);
                    $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
                    $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                    $horari = $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                    $horas = explode('/',
                    $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText())[0];
                    $fct->realizadas = $horas;
                    list($diarias,$ultima) = $this->consultaDiario($driver,$driver->findElement(WebDriverBy::cssSelector("#contenido")));
                    $fct->horas_diarias = $diarias;
                    $fct->actualizacion = fechaSao(substr($ultima,2,10));
                    $fct->save();
                }
            }
        } catch (Exception $e){
            Alert::danger($e);
        }
        $driver->close();
        return back();
    }

    public function download(Request $request){
        $dni = $request->profesor??AuthUser()->dni;
        $grupo = Grupo::where('tutor',$dni)->first();
        if ($ciclo = $grupo->Ciclo)  {
            $driver = RemoteWebDriver::create($this::SERVER_URL, DesiredCapabilities::firefox());
            try {
                $this->login($driver, trim($request->password));
                if ($dni != AuthUser()->dni) {
                    $this->findIndexUser($driver, $dni);
                }
                $table = $driver->findElements(WebDriverBy::cssSelector("tr"));
                $dades = array();
                foreach ($table as $index => $tr) {
                    if ($index) {
                        try {
                            //dades de la linea
                            $dades[$index]['nia'] = $this->getAlumno($tr);
                            list($nameEmpresa, $idEmpresa) = $this->getEmpresa($tr);
                            $dades[$index]['idSao'] = $this->getIdSao($tr);
                            $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles FCT']"))->click();
                            sleep(1);
                            $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
                            $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(7)"));
                            $nameCentre = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(8)"));
                            $dades[$index]['centre']['telefon'] = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $dades[$index]['centre']['email'] = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
                            $dadesInstructor = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(12)"));
                            $dades[$index]['centre']['instructorName'] = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $dades[$index]['centre']['instructorDNI'] = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();

                            list($dades[$index]['periode'],$dades[$index]['desde'],$dades[$index]['hasta']) = $this->getPeriode($detalles);

                            $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                            //$horari = $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $dades[$index]['hores'] = explode('/',
                                $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText())[1];
                            $instructor = Instructor::find($dades[$index]['centre']['instructorDNI']);

                            if ($centro = $this->buscaCentro($nameEmpresa, $idEmpresa, $nameCentre, $dades[$index]['centre']['telefon'],
                                $dades[$index]['centre']['email'], $ciclo->id, $instructor)) {
                                $dades[$index]['centre']['id'] = $centro->id;
                                if ($colaboracion = Colaboracion::where('idCiclo', $ciclo->id)->where('idCentro',
                                    $centro->id)->first()) {
                                    $dades[$index]['colaboracio']['id'] = $colaboracion->id;
                                    $dades[$index]['cicle'] = $ciclo;
                                }
                            }
                            $driver->findElement(WebDriverBy::cssSelector("button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only"))->click();
                        } catch (Exception $e){
                            unset($dades[$index]);
                            Alert::info($e->getMessage());
                        }
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            $driver->close();
        }
        session(compact('dades'));
        if (count($dades)){
            return view('sao.importa',compact('dades'));
        } else {
            return redirect(route('alumnofct.index'));
        }
    }

    public function importa(Request $request){
        $dades = session('dades');
        foreach ($request->request as $key => $value) {
            if ($value == 'on') {
                $centro = Centro::find($dades[$key]['centre']['id']);
                if (!($instructor = Instructor::find($dades[$key]['centre']['instructorDNI']))) {
                    $this->altaInstructor(
                        $dades[$key]['centre']['instructorDNI'],
                        $dades[$key]['centre']['instructorName'],
                        $dades[$key]['centre']['email'],
                        $dades[$key]['centre']['telefon'],
                        $dades[$key]['cicle']
                    );
                }
                $centro->instructores()->syncWithoutDetaching($instructor->dni);
                $fct = Fct::where('idColaboracion', $dades[$key]['colaboracio']['id'])
                    ->where('periode', $dades[$key]['periode'])
                    ->where('idInstructor', $instructor->dni)
                    ->first();
                if (!$fct) {
                    $fct = new Fct([
                        'idColaboracion' => $dades[$key]['colaboracio']['id'],
                        'asociacion' => 1,
                        'idInstructor' => $instructor->dni,
                        'periode' => $dades[$key]['periode']
                    ]);
                    $fct->save();
                }
                $fctAl = AlumnoFct::where('idFct', $fct->id)->where('idAlumno', $dades[$key]['nia'])->first();
                if (!$fctAl) {
                    $fctAl = new AlumnoFct([
                        'horas' => $dades[$key]['hores'],
                        'desde' => fechaSao($dades[$key]['desde']),
                        'hasta' => fechaSao($dades[$key]['hasta']),
                    ]);
                    $fctAl->idFct = $fct->id;
                    $fctAl->idAlumno =  $dades[$key]['nia'];
                }
                $fctAl->idSao =  $dades[$key]['idSao'];
                $fctAl->save();
            }
        }
        return redirect(route('alumnofct.index'));
    }

    private function igual($intranet,$sao){
        if (strtolower($intranet) == strtolower($sao)) return null;
        return array('intranet'=>$intranet,'sao'=>$sao);
    }

    public function check(Request $request)
    {
        $dni = $request->profesor;
        $driver = RemoteWebDriver::create($this::SERVER_URL, DesiredCapabilities::firefox());
        try {
            $this->login($driver, trim($request->password));
            $dades = array();
            foreach (AlumnoFct::misFcts()->activa()->whereNotNull('idSao')->get() as $fctAl) {
                $fct = $fctAl->Fct;
                $centro = $fct->Colaboracion->Centro;
                $empresa = $centro->Empresa;
                if (!isset($dades[$fct->id]['empresa']['idEmpresa'])){
                    $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=10&idFct=$fctAl->idSao");
                    sleep(1);
                    $dades[$fct->id]['nameEmpresa'] = $empresa->nombre;
                    $dades[$fct->id]['nameCentro'] = $centro->nombre;
                    $dades[$fct->id]['empresa']['idEmpresa'] = $driver->findElement(WebDriverBy::cssSelector('#empresaFCT'))->getAttribute('value');
                    $dades[$fct->id]['empresa']['concierto'] = $this->igual($empresa->concierto,$driver->findElement(WebDriverBy::cssSelector('#numConciertoEmp'))->getAttribute('value'));

                    $dadesEmpresa = $driver->findElement(WebDriverBy::cssSelector("td#celdaDatosEmpresa table.infoCentroBD tbody"));
                    $detallesEmpresa = $dadesEmpresa->findElement(WebDriverBy::cssSelector("tr:nth-child(2)"));
                    $dades[$fct->id]['empresa']['cif'] = $this->igual($empresa->cif,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(1)"))->getText());
                    $dades[$fct->id]['empresa']['nombre'] = $this->igual($empresa->nombre,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText());
                    $dades[$fct->id]['empresa']['direccion'] = $this->igual($empresa->direccion,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(3)"))->getText());
                    $dades[$fct->id]['empresa']['localidad'] = $this->igual($empresa->localidad,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText());
                    $detallesEmpresa = $dadesEmpresa->findElement(WebDriverBy::cssSelector("tr:nth-child(4)"));
                    $dades[$fct->id]['empresa']['telefono'] = $this->igual($empresa->telefono,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(1)"))->getText());
                    $dades[$fct->id]['empresa']['gerente'] = $this->igual($empresa->gerente,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText());
                    $dades[$fct->id]['empresa']['actividad'] = $this->igual($empresa->actividad,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(3)"))->getText());
                    $dades[$fct->id]['empresa']['email'] = $this->igual($empresa->email,$detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText());

                    $dadesCentro = $driver->findElement(WebDriverBy::cssSelector("td#celdaDatosCT table.infoCentroBD tbody"));
                    $detallesCentro = $dadesCentro->findElement(WebDriverBy::cssSelector("tr:nth-child(2)"));
                    $dades[$fct->id]['centro']['nombre'] = $this->igual($centro->nombre,$detallesCentro->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText());
                    $dades[$fct->id]['centro']['localidad'] = $this->igual($centro->localidad,$detallesCentro->findElement(WebDriverBy::cssSelector("td:nth-child(3)"))->getText());
                    $dades[$fct->id]['centro']['telefono'] = $this->igual($centro->telefono,$detallesCentro->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText());
                    $dades[$fct->id]['centro']['email'] = $this->igual($centro->email,$detallesCentro->findElement(WebDriverBy::cssSelector("td:nth-child(6)"))->getText());


                    $dades[$fct->id]['centro']['horarios'] = $this->igual($centro->horarios,$driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody tr:nth-child(14) td:nth-child(2)"))->getText());
                    $driver->findElement(WebDriverBy::cssSelector("button.botonRegistro[value='Registrarse']"))->click();
                    sleep(1);
                }
            }
        } catch (Exception $e) {
            Alert::danger($e);
        }
        $driver->close();
        session(compact('dades'));
        if (count($dades)){
            return view('sao.compara',compact('dades'));
        } else {
            return redirect(route('alumnofct.index'));
        }
        return back();
    }
    /**
     * @param  string  $instructorDNI
     * @param  string  $instructorName
     * @param  string  $emailCentre
     * @param  string  $telefonoCentre
     * @param $ciclo
     * @return Instructor
     */
    private function altaInstructor(
        string $instructorDNI,
        string $instructorName,
        string $emailCentre,
        string $telefonoCentre,
        $ciclo
    ): Instructor {

        $instructor = new Instructor([
            'dni' => $instructorDNI,
            'name' => explode(' ', $instructorName)[0],
            'surnames' => substr($instructorName, strlen(explode(' ', $instructorName)[0]),
                strlen($instructorName)),
            'email' => $emailCentre,
            'telefono' => $telefonoCentre,
            'departamento' => $ciclo->ciclo
        ]);
        $instructor->save();
        return $instructor;
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @return void
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    private function login(RemoteWebDriver $driver,$password): void
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
    }

    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebElement  $tr
     * @return string
     */
    private function getAlumno(\Facebook\WebDriver\Remote\RemoteWebElement $tr): string
    {
        $alumne = $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles del alumno/a']"))->getAttribute('href');
        $href = explode('&', $alumne)[1];
        $nia = explode('=', $href)[1];
        return $nia;
    }

    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebElement  $tr
     * @return array
     */
    private function getEmpresa(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array
    {
        $empresa = $tr->findElement(WebDriverBy::cssSelector("a[title^='Detalles de l']"));
        $id = $empresa->getAttribute('href');
        $name = $empresa->getText();
        $href = explode('&', $id)[1];
        $idEmpresa = explode('=', $href)[1];
        return array($name, $idEmpresa);
    }

    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebElement  $tr
     * @return mixed|string
     */
    private function getIdSao(\Facebook\WebDriver\Remote\RemoteWebElement $tr)
    {
        $enlace = $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles FCT']"));
        $href = explode("'", $enlace->getAttribute('href'))[1];
        $fctAl = AlumnoFct::where('idSao',$href)->get()->first();
        if ($fctAl) throw new \Exception("Fct del SAO $href ja donada d'alta");
        return $href;
    }

    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebElement  $detalles
     * @return array
     */
    private function getPeriode(\Facebook\WebDriver\Remote\RemoteWebElement $detalles): array
    {
        $dadesPeriode = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(13)"));
        $periodo = $dadesPeriode->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
        $periode = (substr($periodo, 0, 4) == 'Sept') ? 1 : 2;
        $dates = explode('-', $dadesPeriode->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText());
        $desde = trim($dates[0]);
        $hasta = trim($dates[1]);
        return array($periode, $desde, $hasta);
    }

    private function buscaCentro($nameEmpresa,$idEmpresa,$nameCentro,$telefonoCentre,$emailCentre,$idCiclo,$instructor){
        if ($emailCentre == '') $emailCentre='zz';
        $centro = Centro::where('nombre','like',$nameCentro)->first();
        if (!$centro){
            $centros = array();
            if ($instructor) {
                foreach ($instructor->centros as $centre) {
                    $centros[$centre->id] = 1;
                }
            }
            foreach (Colaboracion::where('telefono','like',$telefonoCentre)->orWhere('email',$emailCentre)->get() as $colaboracion){
                $centros[$colaboracion->idCentro] = isset($centros[$colaboracion->idCentro])?$centros[$colaboracion->idCentro]+1:1;
            }
            foreach (Centro::where('email',$emailCentre)->get() as $centre){
                $centros[$centre->id] = isset($centros[$centre->id])?$centros[$centre->id]+1:1;
            }
            foreach ($centros as $key => $centre){
                $colaboracion = Colaboracion::where('idCentro',$key)->where('idCiclo',$idCiclo)->first();
                if (!$colaboracion) $centros[$key] = 0;
            }
            if (count($centros)){
                arsort($centros);
                if (current($centros) > 0){
                    $centre = key($centros);
                    $centro = Centro::find($centre);
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
        $empresa = Empresa::where('idSao',$idEmpresa)->first();
        if (!$empresa){
            $empresa = Empresa::where('nombre',$nameEmpresa)->first()??($centro?$centro->Empresa:null);
            if ($empresa) {
                $empresa->idSao = $idEmpresa;
                $empresa->save();
            }
        }
        return $centro;
    }

    private function findProfesor($dni,$tableTutores){
        $find = null;
        $tutores = $tableTutores->findElements(WebDriverBy::cssSelector("tr"));
        foreach ($tutores as $index => $tutor){
            if ($index > 0){
                $dniTabla = trim($tutor->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText());
                if ($dniTabla == $dni) {
                    $find = $tutor;
                }
            }
        }
        return $find;
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @param $dni
     * @return void
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    private function findIndexUser(RemoteWebDriver $driver, $dni): void
    {
        $botones = $driver->findElements(WebDriverBy::cssSelector("#botonesFiltroFCT button.botonSelec"));
        $botones[1]->click();
        sleep(1);
        do {
            $tablaTutores = $driver->findElement(WebDriverBy::cssSelector("table.tablaSelEmpresas tbody"));
            $find = $this->findProfesor($dni, $tablaTutores);
            if (!$find) {
                $driver->findElement(WebDriverBy::cssSelector("a[title='Página Siguiente']"))->click();
                sleep(1);
            }
        } while (!$find);
        $find->click();
        sleep(1);
    }

    private function consultaDiario(RemoteWebDriver $driver,\Facebook\WebDriver\Remote\RemoteWebElement $contenido){
        $find = false;
        $i=4;
        do {
            $a = $contenido->findElements(WebDriverBy::cssSelector("#texto_cont p.diasDelDiario a"));
            $hores = trim($contenido->findElement(WebDriverBy::cssSelector("div#diario$i table.tablaDiario tbody tr:nth-child(2) td.celda1:nth-child(4)"))->getText());
            if ($hores > 0) {
                $find = true;
                $dia = explode(',',$a[$i]->getAttribute('href'))[2];
            }
        } while (!$find && $i-- >0);
        if ($find) return array($hores,$dia);
        else{
            $driver->findElement(WebDriverBy::cssSelector("p.celdaInfoAlumno a:nth-child(1)"))->click();
            sleep(1);
            return $this->consultaDiario($driver,$driver->findElement(WebDriverBy::cssSelector("#contenido")));
        }
    }
}