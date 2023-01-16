<?php

namespace Intranet\Http\Controllers;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Entities\Centro;
use Intranet\Entities\Erasmus;
use Intranet\Entities\Grupo;
use Intranet\Entities\Fct;
use DB;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Instructor;
use Intranet\Entities\Profesor;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\AttachedFileService;
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
    protected $server_url;
    const WEB = 'https://foremp.edu.gva.es/index.php';

    public function __construct(){
        $this->server_url = env('SELENIUM_URL','http://172.16.9.10:4444');
        //$this->server_url = env('SELENIUM_URL','http://192.168.56.1:4444');

        return parent::__construct();
    }
    public function index(){
        $tutores = Profesor::tutoresFCT()->orderBy('apellido1')->orderBy('apellido2')->get();
        $action = 'download';
        return view('sao.index',compact('tutores','action'));
    }

    public function post(Request $request)
    {
        $accion = $request->accion;
        return $this->$accion($request);
    }

    public function accion(Request $request,$accion)
    {
        return $this->$accion($request);
    }

    private function alertSuccess(array $alumnes,$message='Sincronitzades Fcts: ') {
        if (count($alumnes)) {
            $tots = '';
            foreach ($alumnes as $alumne) {
                $tots .= $alumne.', ';
            }
            Alert::info($message.$tots);
        }
    }

    public function annexes(Request $request)
    {
        $dni = $request->profesor;
        $driver = RemoteWebDriver::create($this->server_url, DesiredCapabilities::firefox());
        try {
            $this->login($driver, trim($request->password));
            $alumnes = [];
            foreach (AlumnoFctAval::realFcts()->activa()->get() as $fct){
                if ($fct->idSao){
                    $driver->navigate()->to("https://foremp.edu.gva.es/inc/fcts/documentos_fct.php?id={$fct->idSao}&documento=2");
                    sleep(1);
                    try {
                        $name = trim($driver->findElement(WebDriverBy::cssSelector("table.tablaListadoFCTs tbody tr:nth-child(2) td:nth-child(1)"))->getText());
                        $onclick = $driver->findElement(WebDriverBy::cssSelector(".botonSelec[value='Descargar']"))->getAttribute('onclick');
                        $cut1 = explode("'",$onclick);
                        AttachedFileService::saveLink(
                            $name,
                            "https://foremp.edu.gva.es/".$cut1[1],
                            'SAO:Annexe II i III',
                            'zip',
                            "alumnofctaval/$fct->id"
                        );
                        $alumnes[] = $fct->Alumno->shortName;
                    } catch (Exception $e){
                        Alert::info("Annexes de ".$fct->Alumno->fullName." no trobats");
                    }

                    /*$cut1 = explode('=',$onclick);
                    $fct->id_doc = trim($cut1[2],"')");
                    $fct->save();*/
                    $driver->findElement(WebDriverBy::cssSelector(".botonSelec[value='Cerrar']"))->click();
                    sleep(1);
                }
            }

            $this->alertSuccess($alumnes,'Annexes Baixats: ');
        }catch (Exception $e) {
            Alert::danger($e);
        }
        $driver->close();
        return back();
    }

    public function getGerente(){
        $driver = RemoteWebDriver::create($this->server_url, DesiredCapabilities::firefox());
        try {
            $this->login($driver, 'eiclmp5a');
            foreach (Empresa::all() as $empresa) {
                //$empresa = $colaboracion->Centro->Empresa;
                if ($empresa  && $empresa->gerente=='') {
                    try {
                        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?op=4&subop=0&cif=$empresa->cif");
                        sleep(1);
                        $link = $driver->findElement(WebDriverBy::cssSelector("a[title='Modificar']"))->getAttribute('href');
                        sleep(1);
                        $driver->navigate()->to("https://foremp.edu.gva.es/$link");
                        sleep(1);
                        $tabla = $driver->findElement(WebDriverBy::cssSelector("table.formRegAlumno tbody"));
                        $nif = $tabla->findElement(WebDriverBy::cssSelector("input.campoAlumno[name='dni_resp']"))->getAttribute('value');
                        $nom = $tabla->findElement(WebDriverBy::cssSelector("input.campoAlumno[name='nom_resp']"))->getAttribute('value');
                        $ap1 = $tabla->findElement(WebDriverBy::cssSelector("input.campoAlumno[name='ap1_resp']"))->getAttribute('value');
                        $ap2 = $tabla->findElement(WebDriverBy::cssSelector("input.campoAlumno[name='ap2_resp']"))->getAttribute('value');
                        $empresa->gerente = $nif.' '.$nom.' '.$ap1.' '.$ap2;
                        $empresa->save();
                        Alert::info("Empresa $empresa->cif actualitzada");
                    } catch (NoSuchElementException $e) {
                        Alert::info($empresa->cif.' Failed');
                    }
                }
            }
        } catch (Exception $e){
            Alert::danger($e);
        }
        $driver->close();
        return redirect(route('home.profesor'));
    }

    public function sync(Request $request){
        $dni = $request->profesor;
        $driver = RemoteWebDriver::create($this->server_url, DesiredCapabilities::firefox());
        try {
            $this->login($driver, trim($request->password));
            $alumnes = [];
            foreach (AlumnoFctAval::realFcts()->activa()->get() as $fct){
                try {
                    if ($fct->idSao){
                        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=11&idFct=$fct->idSao");
                        sleep(1);
                        $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
                        $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                        $horari = $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                        $horas = explode('/',
                            $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText())[0];
                        if ($fct->realizadas != $horas) {
                            $fct->realizadas = $horas;
                            list($diarias,$ultima) = $this->consultaDiario($driver,$driver->findElement(WebDriverBy::cssSelector("#contenido")));
                            $fct->horas_diarias = $diarias;
                            $fct->actualizacion = fechaSao(substr($ultima,2,10));
                            $fct->save();
                            $alumnes[] = $fct->Alumno->shortName;
                        }
                    }
                } catch (NoSuchElementException $e) {

                }
            }
            $this->alertSuccess($alumnes);
        } catch (Exception $e){
            Alert::danger($e);
        }
        $driver->close();
        return back();
    }

    public function download(Request $request){

        $dni = $request->profesor??AuthUser()->dni;
        $grupo = Grupo::where('tutor',$dni)->first();
        $dades = array();
        if ($ciclo = $grupo->Ciclo) {
            $driver = RemoteWebDriver::create($this->server_url, DesiredCapabilities::firefox());
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
                            $alumne = $this->getAlumno($tr);
                            list($desde, $hasta) = $this->getPeriode($tr);
                            if ($hasta >= Hoy()) {
                                $dades[$index]['nia'] = $alumne;
                                list($nameEmpresa, $idEmpresa) = $this->getEmpresa($tr);
                                $dades[$index]['idSao'] = $this->getIdSao($tr);
                                $dades[$index]['idEmpresa'] = $idEmpresa;
                                $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles FCT']"))->click();
                                sleep(1);
                                $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
                                $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(7)"));
                                $dades[$index]['centre']['localidad'] = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
                                $nameCentre = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                                $dadesCentre = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(8)"));
                                $dades[$index]['centre']['telefon'] = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                                $dades[$index]['centre']['email'] = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
                                //$dades[$index]['centre']['name'] = $dadesCentre->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
                                $dadesInstructor = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(12)"));
                                $dades[$index]['centre']['instructorName'] = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                                $dades[$index]['centre']['instructorDNI'] = $dadesInstructor->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
                                $dades[$index]['desde'] = $desde;
                                $dades[$index]['hasta'] = $hasta;
                                $dades[$index]['autorizacion'] = ($detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(15) td:nth-child(4)"))->getText() == 'No requiere autorización') ? 0 : 1;
                                $dades[$index]['erasmus'] = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(16) td:nth-child(2)"))->getText();
                                $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                                //$horari = $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText();
                                $dades[$index]['hores'] = explode('/',
                                $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText())[1];
                                $instructor = Instructor::find($dades[$index]['centre']['instructorDNI']);
                                $driver->findElement(WebDriverBy::cssSelector("button.ui-button.ui-widget.ui-state-default.ui-corner-all.ui-button-text-only"))->click();
                                sleep(0.5);
                                if ($dades[$index]['erasmus'] == 'No') {
                                    if ($centro = $this->buscaCentro($nameEmpresa, $idEmpresa, $nameCentre,
                                        $dades[$index]['centre']['telefon'],
                                        $dades[$index]['centre']['email'], $ciclo->id, $instructor)) {

                                        $dades[$index]['centre']['id'] = $centro->id;
                                        if ($colaboracion = Colaboracion::where('idCiclo', $ciclo->id)->where('idCentro',
                                            $centro->id)->first()) {
                                            $dades[$index]['colaboracio']['id'] = $colaboracion->id;
                                            $dades[$index]['cicle'] = $ciclo;
                                            $dades[$index]['centre']['idSao'] = $centro->idSao;
                                        } else {
                                            Alert::danger("No trobe col·laboració del centre $nameCentre amb el teu cicle");
                                        }
                                    } else {
                                        $alumno = Alumno::find($dades[$index]['nia']);
                                        Alert::danger("Centro $nameCentre  per alumne $alumno->shorName no trobat. Revisa la col·laboració. Afegix instructor al centre de treball. Revisa el seus dni");
                                    }
                                } else {
                                    $dades[$index]['centre']['name'] = $nameEmpresa;
                                }
                            } else {
                                Alert::info("Fct $alumne finalitzada");
                            }
                        } catch (Exception $e) {
                            unset($dades[$index]);
                            Alert::info($e->getMessage());
                        }
                    }
                }
            }catch (IntranetException $e){
                Alert::warning($e->getMessage());
            }catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        if (count($dades)){
            foreach ($dades as $dada) {
                if (!$dada['centre']['idSao']) {
                    $idEmpresa = $dada['idEmpresa'];
                    $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=19&idEmpresa=$idEmpresa");
                    sleep(1);
                    $table2 = $driver->findElements(WebDriverBy::cssSelector("table.tablaListadoFCTs tbody tr"));
                    foreach ($table2 as $index2 => $trinside) {
                        if ($index2) {
                            $td = trim($trinside->findElement(WebDriverBy::cssSelector("td:nth-child(2)"))->getText());
                            if ($td == $nameCentre) {
                                //$dades[$index]['centro']['direccion'] = trim($trinside->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText());
                                $dada['centre']['idSao'] = substr($trinside->getAttribute('id'), 13);
                            }
                        }
                    }
                }
            }
            $driver->close();
            session(compact('dades'));
            return view('sao.importa',compact('dades'));
        } else {
            $driver->close();
            return redirect(route('alumnofct.index'));
        }
    }

    public function importa(Request $request){
        $dades = session('dades');
        foreach ($request->request as $key => $value) {
            if ($value == 'on') {
                if ($dades[$key]['erasmus'] == 'No') {
                    $centro = Centro::find($dades[$key]['centre']['id']);
                    if (!$centro->idSao) {
                        $centro->idSao = $dades[$key]['centre']['idSao'];
                        $centro->save();
                    }
                    if (!($instructor = Instructor::find($dades[$key]['centre']['instructorDNI']))) {
                        $instructor = $this->altaInstructor(
                            $dades[$key]['centre']['instructorDNI'],
                            $dades[$key]['centre']['instructorName'],
                            $dades[$key]['centre']['email'],
                            $dades[$key]['centre']['telefon'],
                            $dades[$key]['cicle']
                        );
                    }
                    $centro->instructores()->syncWithoutDetaching($instructor->dni);
                    $fct = Fct::where('idColaboracion', $dades[$key]['colaboracio']['id'])
                        ->where('idInstructor', $instructor->dni)
                        ->first();
                    if (!$fct) {
                        $fct = new Fct([
                            'idColaboracion' => $dades[$key]['colaboracio']['id'],
                            'asociacion' => 1,
                            'idInstructor' => $instructor->dni,
                        ]);
                        $fct->save();
                    }
                    $fctAl = AlumnoFct::where('idFct', $fct->id)->where('idAlumno', $dades[$key]['nia'])->first();
                    if (!$fctAl) {
                        $fctAl = new AlumnoFct([
                            'horas' => $dades[$key]['hores'],
                            'desde' => $dades[$key]['desde'],
                            'hasta' => $dades[$key]['hasta'],
                            'autorizacion' => $dades[$key]['autorizacion']
                        ]);
                        $fctAl->idFct = $fct->id;
                        $fctAl->idAlumno = $dades[$key]['nia'];
                    }
                    $fctAl->idSao = $dades[$key]['idSao'];
                    $fctAl->save();
                } else {
                    $fct = Fct::whereNull('idColaboracion')->where('asociacion',2)->first();
                    if (!$fct){
                        $erasmus = Erasmus::find($dades[$key]['idSao']);
                        if (!$erasmus) {
                            $erasmus = new Erasmus();
                            $erasmus->idSao = $dades[$key]['idSao'];
                            $erasmus->name = $dades[$key]['centre']['name'];
                            $erasmus->email = $dades[$key]['centre']['email'];
                            $erasmus->telefon = $dades[$key]['centre']['telefon'];
                            $erasmus->localidad = $dades[$key]['centre']['localidad'];
                            $erasmus->save();
                        }
                        $fct = new Fct([
                            'idColaboracion' => null,
                            'asociacion' => 2,
                            'idInstructor' => $dades[$key]['idSao'],
                        ]);
                        $fct->correoInstructor = 1;
                        $fct->model = 'Erasmus';
                        $fct->save();
                    }
                    $fctAl = AlumnoFct::where('idFct', $fct->id)->where('idAlumno', $dades[$key]['nia'])->first();
                    if (!$fctAl) {
                        $fctAl = new AlumnoFct([
                            'horas' => $dades[$key]['hores'],
                            'desde' => $dades[$key]['desde'],
                            'hasta' => $dades[$key]['hasta'],
                            'autorizacion' => $dades[$key]['autorizacion']
                        ]);
                        $fctAl->idFct = $fct->id;
                        $fctAl->idAlumno = $dades[$key]['nia'];
                    }
                    $fctAl->idSao = $dades[$key]['idSao'];
                    $fctAl->save();
                }
            }
        }
        return redirect(route('alumnofct.index'));
    }

    private function descomposaClau($clau){
        $keyDescomposada = explode('_',$clau);
        $field = $keyDescomposada[2];
        $idFct = $keyDescomposada[1];
        $tipo = $keyDescomposada[0];
        $modelo = ($tipo == 'centro')?
            Fct::find($idFct)->Colaboracion->Centro:
            Fct::find($idFct)->Colaboracion->Centro->Empresa;
        return array($modelo,$field,$idFct,$tipo);

    }

    public function compara(Request $request){
        $dades = session('dades');
        $sao = array();
        foreach ($request->request as $key => $value){
            if ($value == 'sao'){
                list($modelo,$field,$idFct,$tipo) = $this->descomposaClau($key);
                $modelo->$field = $dades[$idFct][$tipo][$field]['sao'];
                $modelo->save();
            }
        }
        return redirect(route('alumnofct.index'));
    }

    private function igual($intranet,$sao){
        if (trim(strtolower(eliminarTildes($intranet))) == trim(strtolower(eliminarTildes($sao)))) return null;
        return array('intranet'=>$intranet,'sao'=>$sao);
    }

    public function check(Request $request)
    {
        $dni = $request->profesor;
        $driver = RemoteWebDriver::create($this->server_url, DesiredCapabilities::firefox());
        $dades = array();
        try {
            $this->login($driver, trim($request->password));
            foreach (AlumnoFct::misFcts()->whereNotNull('idSao')->get() as $fctAl) {
                try {
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
                        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=34&idCT=$centro->idSao");
                        sleep(1);
                        $dades[$fct->id]['centro']['direccion'] = $this->igual($centro->direccion, $driver->findElement(WebDriverBy::cssSelector("input.campoAlumno[name='direccion'"))->getAttribute('value'));
                        $dades[$fct->id]['centro']['codiPostal'] = $this->igual($centro->codiPostal, $driver->findElement(WebDriverBy::cssSelector("input.campoAlumno[name='cp'"))->getAttribute('value'));
                    }
                } catch (NoSuchElementException $e){

                }

            }
            foreach(AlumnoFctAval::misErasmus()->get() as $fctAl){
                $erasmus = Erasmus::where('idSao',$fctAl->idSao)->whereNull('direccio')->first();
                if ($erasmus) {
                    $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=10&idFct=$fctAl->idSao");
                    sleep(1);
                    $dadesEmpresa = $driver->findElement(WebDriverBy::cssSelector("td#celdaDatosEmpresa table.infoCentroBD tbody"));
                    $detallesEmpresa = $dadesEmpresa->findElement(WebDriverBy::cssSelector("tr:nth-child(2)"));
                    $erasmus->direccio = $detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(3)"))->getText();
                    $erasmus->save();
                }
            }
        } catch (Exception $e) {
            Alert::danger($e);
        }
        $driver->close();
        if (count($dades)){
            session(compact('dades'));
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
            'departamento' => isset($ciclo->ciclo)?$ciclo->ciclo:$ciclo
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
        $name = $driver->findElement(WebDriverBy::cssSelector('.botonform'))->getAttribute('name');
        if ($name === 'login'){
            throw new IntranetException('Password no vàlid. Has de ficarl el del SAO');
        }
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

    private function getPeriode(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array
    {
        $dadesPeriode = $tr->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText();
        $dates = explode('-',$dadesPeriode);
        $desde = fechaInglesaCurta(trim($dates[0]),'/');
        $hasta = fechaInglesaCurta(trim($dates[1]),'/');
        return array($desde, $hasta);
    }

    /*private function getPeriode(\Facebook\WebDriver\Remote\RemoteWebElement $detalles): array
    {
        $dadesPeriode = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(13)"));
        $dates = explode('-', $dadesPeriode->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText());
        $desde = trim($dates[0]);
        $hasta = trim($dates[1]);
        return array($desde, $hasta);
    }*/

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
