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
class SaoA2Controller extends SaoController
{

    function download_file_from_url($url, $path)
    {
        $new_file_name = $path;
        $file = fopen ($url, "rb");
        if ($file)
        {
            $newf = fopen ($new_file_name, "wb");
            if ($newf)
                while(!feof($file))
                {
                    fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
                }
        }
        if ($file)
        {
            fclose($file);
        }
        if ($newf)
        {
            fclose($newf);
        }
    }

    public function download_file_from_fcts($driver)
    {
        $fctAl = AlumnoFct::misFcts()->activa()->first();
        $driver->get("https://foremp.edu.gva.es/inc/ajax/generar_pdf.php?doc=2&centro=59&idFct=$fctAl->idSao");
        $driver->close();
        /*
        foreach (AlumnoFct::misFcts()->activa()->get() as $fctAl) {
            $this->download_file_from_url(),'A2.pdf');
         */

    }

    public function index($password)
    {
        $dni = $request->profesor ?? AuthUser()->dni;
        $grupo = Grupo::where('tutor', $dni)->first();
        $ciclo = $grupo->idCiclo??null;

        if (!$ciclo) {
            Alert::danger('No eres tutor');
            return redirect(route('alumnofct.index'));
        } else {
            $driver = RemoteWebDriver::create($this->serverUrl, DesiredCapabilities::firefox());
            try {
                $this->login($driver, $password);
                if ($dni != AuthUser()->dni) {
                    $this->findIndexUser($driver, $dni);
                }
                $this->download_file_from_fcts($driver);
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
                $fct = $this->getFct($dni, $idColaboracion);
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
                    'erasmus' => ($dades['erasmus']=='No')?0:1,
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

    private function findProfesor($dni, $tableTutores)
    {
        $find = null;
        $tutores = $tableTutores->findElements(WebDriverBy::cssSelector("tr"));
        foreach ($tutores as $index => $tutor) {
            if ($index > 0) {
                $dniTabla = trim($tutor->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))->getText());
                if ($dniTabla == $dni) {
                    $find = $tutor;
                }
            }
        }
        return $find;
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
    private function getFct($dni, $idColaboracion): Fct
    {
        $fct = Fct::where('idColaboracion', $idColaboracion)
            ->where('idInstructor', $dni)
            ->where('asociacion', 1)
            ->first();
        if (!$fct) {
            $fct = new Fct([
                'idColaboracion' => $idColaboracion,
                'asociacion' => 1,
                'idInstructor' => $dni,
            ]);
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

}
