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
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoController extends Controller
{
    const SERVER_URL = 'http://192.168.56.1:4444';
    const WEB = 'https://foremp.edu.gva.es/index.php';

    public function createFcts(){
        $driver = RemoteWebDriver::create($this::SERVER_URL, DesiredCapabilities::firefox());
        try {
            $this->login($driver);
            $table = $driver->findElements(WebDriverBy::cssSelector("tr"));
            foreach ($table as $index => $tr){
                if ($index){
                        //dades de la linea
                    $nia = $this->getAlumno($tr);
                    list($name, $idEmpresa) = $this->getCentro($tr);
                    $idSao = $this->getIdSao($tr);
                    $centro = Centro::where('idSao',$idEmpresa)->orWhere('nombre','like',$name)->first();
                    if ($centro) {
                        // click detalls
                        $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles FCT']"))->click();
                        sleep(1);
                        $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));

                        $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(8)"));
                        $telefonoCentre = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                        $emailCentre = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();

                        $dadesInstructor = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(12)"));
                        $instructorName = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                        $instructorDNI = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();

                        list($periode, $desde, $hasta) = $this->getPeriode($detalles);

                        $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                        $horari = $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                        $horas = explode('/',$dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText())[1];


                        $centro->horarios = $horari;
                        $centro->idSao = $idEmpresa;
                        $centro->telefono = $telefonoCentre;
                        $centro->save();
                        $grupo = Grupo::where('tutor',AuthUser()->dni)->first();
                        $ciclo = $grupo->Ciclo??null;
                        if ($ciclo) {
                            $instructor = $this->altaInstructor($instructorDNI, $instructorName, $emailCentre,
                                $telefonoCentre, $ciclo, $centro);

                            $colaboracion = Colaboracion::where('idCiclo',$ciclo->id)->where('idCentro',$centro->id)->first();
                            if (!$colaboracion){
                                $colaboracion = new Colaboracion([
                                    'idCiclo' => $ciclo->id,
                                    'contacto' => $instructorName,
                                    'telefono' => $telefonoCentre,
                                    'puestos'  => 1,
                                    'idCentro' => $centro->id,
                                    'email' => $emailCentre
                                ]);
                                $colaboracion->save();
                            }

                            $fct = Fct::where('idColaboracion',$colaboracion->id)
                                ->where('periode',1)
                                ->where('idInstructor',$instructorDNI)
                                ->first();
                            if (!$fct) {
                                $fct = new Fct([
                                    'idColaboracion' => $colaboracion->id,
                                    'asociacion' => 1,
                                    'idInstructor' => $instructor->dni,
                                    'periode' => $periode
                                ]);
                                $fct->save();
                            }
                            $fctAl = AlumnoFct::where('idFct',$fct->id)->where('idAlumno',$nia)->first();
                            if ($fctAl){
                                $fctAl->idSao = $idSao;
                            } else {
                                $fctAl = new AlumnoFct([
                                    'horas' => $horas,
                                    'desde' => fechaSao($desde),
                                    'hasta' => fechaSao($hasta),
                                ]);
                                $fctAl->idFct = $fct->id;
                                $fctAl->idAlumno = $nia;
                                $fctAl->idSao = $idSao;
                            }
                            $fctAl->save();
                        } else {
                            Alert::info("Ciclo $grupo->codigo no trobat.");
                        }
                    } else {
                        Alert::info("centro $name no trobat.");
                    }
                    $driver->findElement(WebDriverBy::cssSelector("button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only"))->click();
                }
            }
        } catch (\Exception $e){
            echo $e->getMessage();
        }
        $driver->close();
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
        $ciclo,
        $centro
    ): Instructor {
        $instructor = Instructor::find($instructorDNI);
        if (!$instructor) {
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
        }
        $centro->instructores()->syncWithoutDetaching($instructor->dni);
        return $instructor;
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @return void
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    private function login(RemoteWebDriver $driver): void
    {
        $driver->get($this::WEB);
        $dni = substr(AuthUser()->dni, -9);
        $password = trim(AuthUser()->saoPassword);
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
    private function getCentro(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array
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
        $enlace = $tr->findElement(WebDriverBy::cssSelector("a[title='Diario']"));
        $href = explode('&', $enlace->getAttribute('href'))[1];
        $idSao = explode('=', $href)[1];
        return $idSao;
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
}


