<?php

namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Http\Request;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Centro;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Entities\Grupo;
use Intranet\Entities\Instructor;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class Importa
{
    const TD_NTH_CHILD_2 = "td:nth-child(2)";
    const TR_NTH_CHILD_2 = "tr:nth-child(2)";
    const TD_NTH_CHILD_3 = "td:nth-child(3)";
    const TD_NTH_CHILD_4 = "td:nth-child(4)";

    private static function buscaCentro($dada, $empresa)
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

    private static function extractFromModal(&$dades, $index, $tr, $driver)
    {
        list($desde, $hasta) = self::getPeriode($tr);
        $alumne = self::getAlumno($tr);
        if ($hasta >= Hoy()) {
            $dades[$index]['nia'] = $alumne;
            list($nameEmpresa, $idEmpresa) = self::getEmpresa($tr);
            $dades[$index]['idSao'] = self::getIdSao($tr);
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
            $dades[$index]['flexible'] =
                $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(16) td:nth-child(4)"))
                    ->getText();
            try {
                $elementText = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(20) th:nth-child(1)"))
                    ->getText();
                $dades[$index]['dual'] = ($elementText == 'FP Dual') ? 1 : 0;
            } catch (Exception $e) {
                $dades[$index]['dual'] = 0;
            }
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
        }
    }

    /**
     * @param $dada
     * @param  RemoteWebDriver  $driver
     * @param $nameCentre
     * @return mixed
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    private static function extractFromEdit($dada, RemoteWebDriver $driver)
    {
        $idEmpresa = $dada['idEmpresa'];
        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=19&idEmpresa=$idEmpresa");
        sleep(1);
        $concierto = $driver->findElement(WebDriverBy::cssSelector("#tdNumConciertoEmp"))->getText();
        $dada['concierto'] = $concierto;
        $data_conveni = $driver->findElement(WebDriverBy::cssSelector("#tdFechaConciertoEmp"))->getText();
        $date = Date::createFromFormat('d/m/Y', $data_conveni);
        $dada['data_conveni'] = $date->format('Y-m-d');
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

    private static function selectDirectorFct($driver){
        $button = $driver->findElement(WebDriverBy::xpath("//button[contains(@class, 'botonSelec') and text()='Tutor/a...']"));
        $button->click();
        sleep(1);
        $selectElement = $driver->findElement(WebDriverBy::id('selecFiltroProfesores'));
        $select = new WebDriverSelect($selectElement);
        $select->selectByValue('nombre');
        $wait = new WebDriverWait($driver, 10);
        $inputElement = $wait->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('filtroProfesores'))
        );
        $inputElement->sendKeys(AuthUser()->apellido1);
        $link = $driver->findElement(WebDriverBy::cssSelector('a[title="Filtrar"]'));
        $link->click();
        sleep(1);
        $table = $driver->findElement(WebDriverBy::className('tablaSelEmpresas'));
        $rows = $table->findElements(WebDriverBy::cssSelector("tr"));
        foreach ($rows as $key => $row) {
            if ($key == 0) {
                continue;
            }
            $segonaColumna = $row->findElement(WebDriverBy::cssSelector('td:nth-child(2)'));
            if ($segonaColumna->getText() ==" ".AuthUser()->dni) {
                break;
            }
        }

        $table->findElement(WebDriverBy::cssSelector('tr:nth-child('.$key.')'))->click();
        sleep(1);
    }

    public static function index($driver)
    {
        $grupo = Grupo::where('tutor', AuthUser()->dni)->first();
        $ciclo = $grupo->idCiclo??null;
        $dades = array();
        if (AuthUser()->dni === config('avisos.director')) {
            self::selectDirectorFct($driver);
        }

        try {
            self::extractPage($driver, $dades,1);
            $driver->findElement(WebDriverBy::cssSelector("a.enlacePag"))->click();
            sleep(1);
            self::extractPage($driver, $dades,2);
        } catch (Exception $e) {
            //No hi ha més pàgines
        }


        if (count($dades)) {
            foreach ($dades as $index => $dada) {
                try {
                    $dades[$index] = self::extractFromEdit($dada, $driver);
                    $empresa = Empresa::where('cif', $dades[$index]['cif'])->first();

                    if ($empresa) { //Si hi ha empresa
                        $dades[$index]['centre']['id'] = self::buscaCentro($dades[$index], $empresa);
                        if ($empresa->data_signatura < $dades[$index]['data_conveni']) {
                            $empresa->data_signatura = $dades[$index]['data_conveni'];
                            $empresa->save();
                        }
                        if (!$empresa->concierto){
                            $empresa->concierto = $dades[$index]['concierto'];
                            $empresa->save();
                        }
                    }
                } catch (Exception $e) {
                    Alert::info($e->getMessage());
                }
            }
        }
        $driver->quit();
        session(compact('dades'));
        return view('sao.importa', compact('dades', 'ciclo'));
    }


    public function importa(Request $request)
    {
        $dades = session('dades');
        $ciclo = $request->ciclo;
        foreach ($request->request as $key => $value) {
            if ($value == 'on') {
                $centro = self::getCentro($dades[$key]);
                $idColaboracion = self::getColaboracion($dades[$key], $ciclo, $centro->id);
                $dni = self::getDni($centro, $dades[$key], $ciclo);
                $asociacion = $dades[$key]['dual'] ? 4 : ($dades[$key]['erasmus'] == 'No' ? 1 : 2);
                $fct = self::getFct($dni, $idColaboracion, $asociacion);
                self::saveFctAl($fct, $dades[$key]);
            }
        }
        return redirect(route('alumnofct.index'));
    }

    private static function getCentro($dades)
    {

        $idCentro = $dades['centre']['id']??null;

        if ($idCentro) {
            return Centro::find($idCentro);
        } else {
            $idSao = $dades['centre']['idSao']??null;
            if ($idSao) {
                $centro = Centro::where('idSao', $idSao)->first();
                if ($centro) {
                    $centro->Empresa->update(['idSao' => $dades['idEmpresa']]);
                    return $centro;
                }
            }
        }

        if (!$empresa = Empresa::where('cif', $dades['cif'])->get()->first()) {
            $empresa = new Empresa(
                [
                    'cif' => $dades['cif'],
                    'concierto' => $dades['concierto'],
                    'nombre' => $dades['nameEmpresa'],
                    'idSao' => $dades['idEmpresa'],
                    'email' => $dades['centre']['email'],
                    'localidad' => $dades['centre']['localidad'],
                    'telefono' => $dades['centre']['telefon'],
                    'europa' => ($dades['erasmus']=='No')?0:1,
                    'observaciones' => 'Empresa creada automàticament',
                    'sao' => 1,
                    'direccion' => '',
                    'data_signatura' => $dades['data_conveni'],
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

    private static function getColaboracion($dada, $idCiclo, $idCentro)
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
    private static function getAlumno(\Facebook\WebDriver\Remote\RemoteWebElement $tr): string
    {
        $alumne = $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles del alumno/a']"))->getAttribute('href');
        $href = explode('&', $alumne)[1];
        return explode('=', $href)[1];
    }



    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebElement  $tr
     * @return array
     */
    private static function getEmpresa(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array
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
    private static function getIdSao(\Facebook\WebDriver\Remote\RemoteWebElement $tr)
    {
        $enlace = $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles Formación Empresa']"));
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

    private static function getPeriode(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array
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
    private static function altaInstructor(
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
    private static function getDni($centro, $dades, $ciclo)
    {
        if (!$centro->idSao) {
            $centro->idSao = $dades['centre']['idSao'];
            $centro->save();
        }
        if (!($instructor = Instructor::find($dades['centre']['instructorDNI']))) {
            $instructor = self::altaInstructor(
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
    private static function getFct($dni, $idColaboracion, $asociacion): Fct
    {
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
    private static function saveFctAl(Fct $fct, $dades)
    {
        $fctAl = AlumnoFct::where('idFct', $fct->id)
            ->where('idAlumno', $dades['nia'])
            ->where('idSao', $dades['idSao'])
            ->first();
        if (!$fctAl) {
            $fctAl = new AlumnoFct([
                'horas' => $dades['hores'],
                'desde' => $dades['desde'],
                'hasta' => $dades['hasta'],
            ]);
            $fctAl->idFct = $fct->id;
            $fctAl->idAlumno = $dades['nia'];
        }
        $fctAl->desde = $dades['desde'];
        $fctAl->hasta = $dades['hasta'];
        $fctAl->horas = $dades['hores'];
        $fctAl->flexible = $dades['flexible'] == 'No' ? 0 : 1;
        $fctAl->autorizacion = $dades['autorizacion'];
        $fctAl->idSao = $dades['idSao'];
        $fctAl->idProfesor = authUser()->dni;
        $fctAl->save();
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @param  array  $dades
     * @return array
     */
    private static function extractPage(RemoteWebDriver $driver, array &$dades,$page)
    {
        $table = $driver->findElements(WebDriverBy::cssSelector("tr"));
        foreach ($table as $index => $tr) {
            if ($index) { //el primer és el titol i no cal iterar-lo
                $key = ($page-1) * 30 + $index;
                try {
                    //dades de la linea
                    self::extractFromModal($dades, $key, $tr, $driver);
                } catch (Exception $e) {
                    unset($dades[$key]);
                    Alert::info($e->getMessage());
                }
            }
        }
    }

}
