<?php

namespace Intranet\Http\Controllers;

use Exception;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Http\Request;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Centro;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;
use Intranet\Entities\Grupo;
use Intranet\Entities\Empresa;
use Intranet\Entities\Instructor;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoImportaController extends SaoController
{
    private function buscaCentro($dada, $empresa)
    {
        $idEmpresa = $empresa->id;
        $nameCentro = $dada['centre']['name'];
        $idSao = $dada['centre']['idSao'];
        $telefonoCentre = $dada['centre']['telefon'];
        $emailCentre = $dada['centre']['email'];

        $centros = array();
        if ($centro = Centro::where('nombre', 'like', $nameCentro)
            ->where('idEmpresa', $idEmpresa)
            ->first()) {
            $centros[$centro->id] = 2;
        }

        if ($centro = Centro::where('idSao', $idSao)
            ->first()) {
            $centros[$centro->id] = isset($centros[$centro->id])?$centros[$centro->id]+2:2;
        }

        if ($instructor = Instructor::find($dada['centre']['instructorDNI'])) {
            foreach ($instructor->centros as $centre) {
                if ($centre->idEmpresa == $idEmpresa) {
                    $centros[$centre->id] = isset($centros[$centre->id])?$centros[$centre->id]+2:2;
                }
            }
        }
        foreach (Colaboracion::miColaboracion()
                     ->where('telefono', 'like', $telefonoCentre)
                     ->get() as $colaboracion) {
            if ($colaboracion->Centro->idEmpresa == $idEmpresa) {
                $centros[$colaboracion->idCentro] =
                    isset($centros[$colaboracion->idCentro]) ?
                        $centros[$colaboracion->idCentro] + 1 :
                        1;
            }
        }

        foreach (Centro::where('email', $emailCentre)->get() as $centre) {
            if ($centre->idEmpresa == $idEmpresa) {
                $centros[$centre->id] = isset($centros[$centre->id]) ? $centros[$centre->id] + 1 : 1;
            }
        }
        if (count($centros)) {
            $value = max($centros);
            return array_search($value, $centros);
        } else {
            return null;
        }

    }

    private function extractFromModal(&$dades, $index, $tr, $driver)
    {
        list($desde, $hasta) = $this->getPeriode($tr);
        $alumne = $this->getAlumno($tr);
        if ($hasta >= Hoy()) {
            $dades[$index]['nia'] = $alumne;
            list($nameEmpresa, $idEmpresa) = $this->getEmpresa($tr);
            $dades[$index]['idSao'] = $this->getIdSao($tr);
            $dades[$index]['idEmpresa'] = $idEmpresa;
            $dades[$index]['nameEmpresa'] = $nameEmpresa;
            $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles FCT']"))->click();
            sleep(1);
            $detalles = $driver
                ->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
            $dadesCentre = $detalles
                ->findElement(WebDriverBy::cssSelector("tr:nth-child(7)"));
            $dades[$index]['centre']['localidad'] =
                $dadesCentre
                    ->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_4))
                    ->getText();
            $nameCentre =
                $dadesCentre
                    ->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))
                    ->getText();
            $dades[$index]['centre']['name'] = $nameCentre;
            $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(8)"));
            $dades[$index]['centre']['telefon'] =
                $dadesCentre
                    ->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))
                    ->getText();
            $dades[$index]['centre']['email'] =
                $dadesCentre
                    ->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_4))
                    ->getText();
            $dadesInstructor =
                $detalles
                    ->findElement(WebDriverBy::cssSelector("tr:nth-child(12)"));
            $dades[$index]['centre']['instructorName'] =
                $dadesInstructor
                    ->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))
                    ->getText();
            $dades[$index]['centre']['instructorDNI'] =
                $dadesInstructor
                    ->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_4))
                    ->getText();
            $dades[$index]['desde'] = $desde;
            $dades[$index]['hasta'] = $hasta;
            $dades[$index]['autorizacion'] =
                ($detalles
                        ->findElement(WebDriverBy::cssSelector("tr:nth-child(15) td:nth-child(4)"))
                        ->getText()
                    ==
                    'No requiere autorización') ? 0 : 1;
            $dades[$index]['erasmus'] =
                $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(16) td:nth-child(2)"))
                    ->getText();
            $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
            $dades[$index]['hores'] = explode(
                '/',
                $dadesHores->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_4))->getText()
            )[1];
            $driver->findElement(
                WebDriverBy::cssSelector(
                    "button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only"
                )
            )->click();
            sleep(0.5);
        } else {
            Alert::info("Fct $alumne finalitzada");
        }
    }

    /**
     * @param $dada
     * @param  RemoteWebDriver  $driver
     * @param $nameCentre
     * @return mixed
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    private function extractFromEdit($dada, RemoteWebDriver $driver)
    {
        $idEmpresa = $dada['idEmpresa'];
        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=19&idEmpresa=$idEmpresa");
        sleep(1);
        $table1 = $driver
            ->findElement(WebDriverBy::cssSelector("table.infoUsuario.infoEmpresa tbody tr:nth-child(2)"));
        $cif = $table1
            ->findElement(WebDriverBy::cssSelector("td:nth-child(1)"))->getText();
        $dada['cif'] = $cif;
        $table2 = $driver->findElements(WebDriverBy::cssSelector("table.tablaListadoFCTs tbody tr"));
        foreach ($table2 as $index2 => $trinside) {
            if ($index2) {
                $td = trim($trinside
                    ->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))
                    ->getText()
                );
                if ($td == $dada['centre']['name']) {
                    $dada['centre']['idSao'] = substr($trinside->getAttribute('id'), 13);
                }
            }
        }
        return $dada;
    }

    public function index($password)
    {
        $dni = AuthUser()->dni;
        $grupo = Grupo::where('tutor', $dni)->first();
        $ciclo = $grupo->idCiclo??null;
        $dades = array();

        if (!$ciclo) {
            Alert::danger('No eres tutor');
            return redirect(route('alumnofct.index'));
        } else {
            $driver = RemoteWebDriver::create($this->serverUrl, DesiredCapabilities::firefox());
            try {
                $this->login($driver, $password);
                try {
                    $this->extractPage($driver, $dades);
                    $driver->findElement(WebDriverBy::cssSelector("a.enlacePag"))->click();
                    sleep(1);
                    $this->extractPage($driver, $dades);
                } catch (Exception $e) {
                    //No hi ha més pàgines
                }


                if (count($dades)) {
                    foreach ($dades as $index => $dada) {
                        try {
                            $dades[$index] = $this->extractFromEdit($dada, $driver);
                            $empresa = Empresa::where('cif', $dades[$index]['cif'])->first();
                            if ($empresa) { //Si hi ha empresa
                                $dades[$index]['centre']['id'] = $this->buscaCentro($dades[$index], $empresa);
                            }
                        } catch (Exception $e) {
                            Alert::info($e->getMessage());
                        }
                    }
                }
                $driver->close();
                session(compact('dades'));
                return view('sao.importa', compact('dades', 'ciclo'));
            } catch (Exception $e) {
                Alert::warning($e->getMessage());
                $driver->close();
                return redirect(route('alumnofct.index'));
            }
        }
    }


    public function importa(Request $request)
    {
        $dades = session('dades');
        $ciclo = $request->ciclo;
        foreach ($request->request as $key => $value) {
            if ($value == 'on') {
                $centro = $this->getCentro($dades[$key]);
                $idColaboracion = $this->getColaboracion($dades[$key], $ciclo, $centro->id);
                $dni = $this->getDni($centro, $dades[$key], $ciclo);
                $fct = $this->getFct($dni, $idColaboracion, $dades[$key]['erasmus']);
                $this->saveFctAl($fct, $dades[$key]);
            }
        }
        return redirect(route('alumnofct.index'));
    }

    private function getCentro($dades)
    {
        $idCentro = $dades['centre']['id']??null;

        if ($idCentro) {
            return Centro::find($idCentro);
        }

        if (!$empresa = Empresa::where('cif', $dades['cif'])->get()->first()) {
            $empresa = new Empresa(
                [
                    'cif' => $dades['cif'],
                    'nombre' => $dades['nameEmpresa'],
                    'idSao' => $dades['idEmpresa'],
                    'email' => $dades['centre']['email'],
                    'localidad' => $dades['centre']['localidad'],
                    'telefono' => $dades['centre']['telefon'],
                    'europa' => ($dades['erasmus']=='No')?0:1,
                    'observaciones' => 'Empresa creada automàticament',
                    'sao' => 1,
                    'direccion' => ''
                ]
            );
            $empresa->save();
        }

        $centro = new Centro(
            [
                'idEmpresa' => $empresa->id,
                'localidad' => $dades['centre']['localidad'],
                'email' =>  $dades['centre']['email'],
                'telefono' => $dades['centre']['telefon'],
                'nombre' =>  $dades['centre']['name'],
                'observaciones' => 'Creada automàticament',
                'idSao' => $dades['centre']['idSao']
            ]
        );
        $centro->save();
        return $centro;
    }

    private function getColaboracion($dada, $idCiclo, $idCentro)
    {
        if (!$colaboracion = Colaboracion::where('idCiclo', $idCiclo)
            ->where('idCentro', $idCentro)
            ->get()
            ->first()
        ) {
            $colaboracion = new Colaboracion(
                [
                    'idCiclo' => $idCiclo,
                    'idCentro' => $idCentro,
                    'contacto' => 'Creada automàticament',
                    'tutor' => authUser()->dni,
                    'telefono' => $dada['centre']['telefon']??'',
                    'puestos' => 1,
                    'email' => $dada['centre']['email']??'',
                ]
            );
            $colaboracion->save();
        }
        return $colaboracion->id;
    }

    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebElement  $tr
     * @return string
     */
    private function getAlumno(\Facebook\WebDriver\Remote\RemoteWebElement $tr): string
    {
        $alumne = $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles del alumno/a']"))->getAttribute('href');
        $href = explode('&', $alumne)[1];
        return explode('=', $href)[1];
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
        $fctAl = AlumnoFct::where('idSao', $href)->where('beca', 0)->get()->first();
        if ($fctAl) {
            throw new \Exception("Fct del SAO $href ja donada d'alta");
        }
        return $href;
    }

    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebElement  $detalles
     * @return array
     */

    private function getPeriode(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array
    {
        $dadesPeriode = $tr->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
        $dates = explode('-', $dadesPeriode);
        $desde = fechaInglesaCurta(trim($dates[0]), '/');
        $hasta = fechaInglesaCurta(trim($dates[1]), '/');
        return array($desde, $hasta);
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
            'surnames' => substr(
                $instructorName,
                strlen(explode(' ', $instructorName)[0]),
                strlen($instructorName)
            ),
            'email' => $emailCentre,
            'telefono' => $telefonoCentre,
            'departamento' => isset($ciclo->ciclo)?$ciclo->ciclo:$ciclo
        ]);
        $instructor->save();
        return $instructor;
    }


    /**
     * @param $centro
     * @param $dades
     * @return mixed
     */
    private function getDni($centro, $dades, $ciclo)
    {
        if (!$centro->idSao) {
            $centro->idSao = $dades['centre']['idSao'];
            $centro->save();
        }
        if (!($instructor = Instructor::find($dades['centre']['instructorDNI']))) {
            $instructor = $this->altaInstructor(
                $dades['centre']['instructorDNI'],
                $dades['centre']['instructorName'],
                $dades['centre']['email'],
                $dades['centre']['telefon'],
                Ciclo::find($ciclo)
            );
        }
        $dni = ($instructor->dni == 0) ? $dades['centre']['instructorDNI'] : $instructor->dni;
        $centro->instructores()->syncWithoutDetaching($dni);
        return $dni;
    }

    /**
     * @param $id
     * @param $dni
     * @param $idColaboracion
     * @return Fct
     */
    private function getFct($dni, $idColaboracion, $erasmus): Fct
    {
        $asociacion = $erasmus == 'No' ? 1 : 2;
        $fct = Fct::where('idColaboracion', $idColaboracion)
            ->where('idInstructor', $dni)
            ->where('correoInstructor', 0)
            ->where('asociacion', $asociacion)
            ->first();
        if (!$fct) {
            $col = Colaboracion::find($idColaboracion);
            $fct = new Fct([
                'idColaboracion' => $idColaboracion,
                'asociacion' => $asociacion,
                'idInstructor' => $dni,
            ]);
            $fct->cotutor = $col->Propietario?$col->tutor:null;
            $fct->save();
        }
        return $fct;
    }

    /**
     * @param  Fct  $fct
     * @param $dades
     * @return AlumnoFct
     */
    private function saveFctAl(Fct $fct, $dades)
    {
        $fctAl = AlumnoFct::where('idFct', $fct->id)->where('idAlumno', $dades['nia'])->first();
        if (!$fctAl) {
            $fctAl = new AlumnoFct([
                'horas' => $dades['hores'],
                'desde' => $dades['desde'],
                'hasta' => $dades['hasta'],
                'autorizacion' => $dades['autorizacion']
            ]);
            $fctAl->idFct = $fct->id;
            $fctAl->idAlumno = $dades['nia'];
        }
        $fctAl->idSao = $dades['idSao'];
        $fctAl->save();
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @param  array  $dades
     * @return array
     */
    private function extractPage(RemoteWebDriver $driver, array &$dades)
    {
        $table = $driver->findElements(WebDriverBy::cssSelector("tr"));
        foreach ($table as $index => $tr) {
            if ($index) { //el primer és el titol i no cal iterar-lo
                try {
                    //dades de la linea
                    $this->extractFromModal($dades, $index, $tr, $driver);
                } catch (Exception $e) {
                    unset($dades[$index]);
                    Alert::info($e->getMessage());
                }
            }
        }
    }

}
