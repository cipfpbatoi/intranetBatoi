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
        return view('sao.index',compact('tutores'));
    }


    public function createFcts(Request $request){
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
                foreach ($table as $index => $tr) {
                    if ($index) {
                        try {
                            //dades de la linea
                            $nia = $this->getAlumno($tr);
                            list($nameEmpresa, $idEmpresa) = $this->getEmpresa($tr);
                            $idSao = $this->getIdSao($tr);
                            $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles FCT']"))->click();
                            sleep(1);
                            $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
                            $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(7)"));
                            $nameCentre = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(8)"));
                            $telefonoCentre = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $emailCentre = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
                            $dadesInstructor = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(12)"));
                            $instructorName = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $instructorDNI = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();

                            list($periode, $desde, $hasta) = $this->getPeriode($detalles);

                            $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                            $horari = $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                            $horas = explode('/',
                                $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText())[1];
                            $instructor = Instructor::find($instructorDNI);

                            $centro = $this->buscaCentro($nameEmpresa, $idEmpresa, $nameCentre, $telefonoCentre,
                                $emailCentre, $ciclo->id, $instructor);
                            if (!$centro) throw new \Exception("No trobat centre per a $nameCentre")
                                // click detalls
                                /*
                                $centro->horarios = $horari;
                                $centro->telefono = $telefonoCentre;
                                $centro->save();
                                */

                                $instructor = $instructor ?? $this->altaInstructor($instructorDNI, $instructorName,
                                    $emailCentre,
                                    $telefonoCentre, $ciclo);
                                $centro->instructores()->syncWithoutDetaching($instructorDNI);
                                $colaboracion = Colaboracion::where('idCiclo', $ciclo->id)->where('idCentro',
                                    $centro->id)->first();
                                if (!$colaboracion) throw new \Exception("No trobada colaboració per al centre $centro->nombre");
                                /*if (!$colaboracion) {
                                    $colaboracion = new Colaboracion([
                                        'idCiclo' => $ciclo->id,
                                        'contacto' => $instructorName,
                                        'telefono' => $telefonoCentre,
                                        'puestos' => 1,
                                        'idCentro' => $centro->id,
                                        'email' => $emailCentre
                                    ]);
                                    $colaboracion->save();
                                }*/

                                $fct = Fct::where('idColaboracion', $colaboracion->id)
                                    ->where('periode', $periode)
                                    ->where('idInstructor', $instructorDNI)
                                    ->first();
                                if (!$fct) {
                                    $fct = new Fct([
                                        'idColaboracion' => $colaboracion->id,
                                        'asociacion' => 1,
                                        'idInstructor' => $instructorDNI,
                                        'periode' => $periode
                                    ]);
                                    $fct->save();
                                }
                                $fctAl = AlumnoFct::where('idFct', $fct->id)->where('idAlumno', $nia)->first();
                                if (!$fctAl) {
                                    $fctAl = new AlumnoFct([
                                        'horas' => $horas,
                                        'desde' => fechaSao($desde),
                                        'hasta' => fechaSao($hasta),
                                    ]);
                                    $fctAl->idFct = $fct->id;
                                    $fctAl->idAlumno = $nia;
                                }
                                $fctAl->idSao = $idSao;
                                $fctAl->save();
                        } catch (\Exception $e){
                            Alert::danger($e->getMessage());
                        }
                        $driver->findElement(WebDriverBy::cssSelector("button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only"))->click();
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            $driver->close();
        }
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
        $fctAl = AlumnoFct::where('idSao',$href);
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
        if ($centro) Alert::info("$centro->nombre per Nom");
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
}


