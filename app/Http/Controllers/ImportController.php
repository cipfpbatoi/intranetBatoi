<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
Use Intranet\Entities\Alumno;
Use Intranet\Entities\Profesor;
use Intranet\Entities\Horario;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Espacio;
use DB;
use Illuminate\Database\Seeder;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Programacion;
use Intranet\Entities\Ocupacion;
use Illuminate\Support\Str;

/**
 * Class ImportController
 * @package Intranet\Http\Controllers
 */
class ImportController extends Seeder
{
    /**
     * @var
     */
    private $plantilla;
    /**
     * @var array
     */
    private $campos_bd_xml = array(
        array('nombrexml' => 'alumnos',
            'nombreclase' => 'Alumno',
            'id' => 'NIA',
            'filtro' => ['estado_matricula', '<>', 'B'],
            'update' => array(
                'dni' => 'hazDNI,documento,NIA',
                'nia' => 'NIA',
                'nombre' => 'nombre',
                'apellido1' => 'apellido1',
                'apellido2' => 'apellido2',
                'fecha_nac' => 'getFechaFormatoIngles,fecha_nac',
                'sexo' => 'sexo',
                'expediente' => 'expediente',
                'domicilio' => 'hazDomicilio,tipo_via,domicilio,numero,puerta,escalera,letra,piso',
                'codigo_postal' => 'cod_postal',
                'provincia' => 'provincia',
                'municipio' => 'municipio',
                'telef1' => 'digitos,telefono1',
                'telef2' => 'digitos,telefono2',
                'fecha_ingreso' => 'getFechaFormatoIngles,fecha_ingreso_centro',
                'fecha_matricula' => 'getFechaFormatoIngles,fecha_matricula',
                'repite' => 'repite',
                'turno' => 'turno',
                'trabaja' => 'trabaja',
                'password' => 'cifrar,documento',
                'baja' => null,
            ),
            'create' => array(
                'email' => 'email1'
            )),
        array('nombrexml' => 'docentes',
            'nombreclase' => 'Profesor',
            'id' => 'documento',
            'update' => array(
                'nombre' => 'nombre',
                'apellido1' => 'apellido1',
                'apellido2' => 'apellido2',
                'sexo' => 'sexo',
                'codigo_postal' => 'cod_postal',
                'domicilio' => 'domicilio',
                'movil1' => 'digitos,telefono1',
                'movil2' => 'digitos,telefono2',
                'emailItaca' => 'email1',
                'sustituye_a' => 'titular_sustituido',
                'fecha_nac' => 'getFechaFormatoIngles,fecha_nac',
                'fecha_ingreso' => 'getFechaFormatoIngles,fecha_ingreso',
                'fecha_ant' => 'getFechaFormatoIngles,fecha_antiguedad',
                'activo' => true,
            ),
            'create' => array(
                'codigo' => 'crea_codigo_profesor,0',
                'dni' => 'documento',
                'email' => 'email,nombre,apellido1',
                'departamento' => '99',
                'password' => 'cifrar,documento',
                'api_token' => 'aleatorio,60'
            )),
        array('nombrexml' => 'grupos',
            'nombreclase' => 'Grupo',
            'id' => 'codigo',
            'update' => array(
                'nombre' => 'nombre',
                'turno' => 'turno',
                'tutor' => 'tutor_ppal',
            ),
            'create' => array(
                'codigo' => 'codigo',
            )),
        array('nombrexml' => 'alumnos',
            'nombreclase' => 'AlumnoGrupo',
            'filtro' => ['estado_matricula', '<>', 'B'],
            'id' => 'NIA,grupo',
            'required' => ['NIA', 'grupo'],
            'update' => array(
                'idAlumno' => 'NIA',
                'idGrupo' => 'grupo'
            ),
            'create' => array(
            )),
        array('nombrexml' => 'aulas',
            'nombreclase' => 'Espacio',
            'id' => 'codigo',
            'update' => array(
                'descripcion' => 'nombre',
            ),
            'create' => array(
                'aula' => 'codigo',
                'idDepartamento' => '99',
            )),
        array('nombrexml' => 'ocupaciones',
            'nombreclase' => 'Ocupacion',
            'id' => 'codigo',
            'update' => array(
                'nombre' => 'nombre_cas',
                'nom' => 'nombre_val'
            ),
            'create' => array(
                'codigo' => 'codigo'
            )),
        array('nombrexml' => 'contenidos',
            'nombreclase' => 'Modulo',
            'id' => 'codigo',
            'update' => array(
                'cliteral' => 'nombre_cas',
                'vliteral' => 'nombre_val'
            ),
            'create' => array(
                'codigo' => 'codigo'
            )),
        array('nombrexml' => 'horarios_grupo',
            'nombreclase' => 'Horario',
            'id' => '',
            'update' => array(
            ),
            'create' => array(
                'dia_semana' => 'dia_semana',
                'plantilla' => 'plantilla',
                'sesion_orden' => 'sesion_orden',
                'idProfesor' => 'docente',
                'modulo' => 'contenido',
                'idGrupo' => 'grupo',
                'aula' => 'aula',
            )),
        array('nombrexml' => 'horarios_ocupaciones',
            'nombreclase' => 'Horario',
            'id' => '',
            'update' => array(
            ),
            'create' => array(
                'dia_semana' => 'dia_semana',
                'sesion_orden' => 'sesion_orden',
                'plantilla' => 'plantilla',
                'idProfesor' => 'docente',
                'ocupacion' => 'ocupacion'
            )),
    );

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('seeder.create');
    }

    /**
     * @param $nombre
     * @param $apellido
     * @return string
     */
    public function email($nombre, $apellido)
    {
        return strtolower(substr($nombre, 0, 1) . $apellido . '@' . config('contacto.host.dominio'));
    }

    /**
     * @return string
     */
    public function aleatorio()
    {
        return Str::random(60);
    }

    public function hazDNI($dni,$nia){
        if (strlen($dni) > 8) return $dni;
        $alumno = Alumno::find($nia);
        if ($alumno) return $alumno->dni;
        else {
            $dniFictici = 'F'.Str::random(9);
            Alert::warning('Alumne amb DNI Fictici '.$dniFictici);
            return $dniFictici;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (!$request->hasFile('fichero') || !file_exists($request->file('fichero'))) {
            Alert::danger(trans('messages.generic.noFile'));
            return back();
        }
        $extension = $request->file('fichero')->getClientOriginalExtension();
        if (!$request->file('fichero')->isValid() || $extension <> 'xml') {
            Alert::danger(trans('messages.generic.invalidFormat'));
            return back();
        }

        ini_set('max_execution_time', 360);
        $this->run($request->file('fichero'), $request);
        ini_set('max_execution_time', 30);
        $this->asignarTutores(false);

        return view('seeder.store');
    }

    /**
     * @param bool $back
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarTutores($back =  true)
    {
        foreach (Profesor::all() as $profesor) {
            $profesor->rol = $this->assignRole(Grupo::QTutor($profesor->dni)->first(),$profesor->rol);
            $profesor->save();
        }
        Alert:info('Tutors assignats');
        if ($back) {
            return back();
        }
    }

    /**
     * @param $grupo
     * @param $role
     * @return bool|\Illuminate\Config\Repository|mixed
     */
    private function assignRole($grupo, $role){
        $rolTutor=  config('roles.rol.tutor');
        $rolPracticas = config('roles.rol.practicas');
        if ($grupo) {
            if (!esRol($role, $rolTutor)) {
                $role *= $rolTutor;
            }
            if ($grupo->curso == 2 && !esRol($role, $rolPracticas)) {
                $role *= $rolPracticas;
            }
            if ($grupo->curso == 1 && esRol($role, $rolPracticas)) {
                $role /= $rolPracticas;
            }
            return $role;
        }
        if (esRol($role, $rolTutor)) {
            $role /= $rolTutor;
        }
        if (esRol($role, $rolPracticas)) {
            $role /= $rolPracticas;
        }
        return $role;
    }

    /**
     * @param $fxml
     * @param Request $request
     */
    public function run($fxml, Request $request)
    {
        $xml = simplexml_load_file($fxml);
        foreach ($this->campos_bd_xml as $table) {
            $this->manageTable($xml->{$table['nombrexml']}, $table, $request->primera);
        }

    }

    /**
     * @param $xmltable
     * @param $table
     * @param $firstImport
     */
    private function manageTable($xmltable, $table, $firstImport){
        if (count($xmltable)) {
            $this->pre($table['nombreclase'], $table['nombrexml']);
            $this->in($xmltable, $table);
            $this->post($table['nombreclase'], $table['nombrexml'], $firstImport);
        } else {
            Alert::danger('No hay registros de ' . $table['nombrexml'] . ' en el xml');
        }
    }

    /**
     * @param $clase
     * @param $xml
     */
    private function pre($clase, $xml)
    {
        switch ($clase) {
            case 'Alumno':

                $this->alumnosBaja();
                break;
            case 'Profesor' : $this->profesoresBaja();
                break;
            case 'Grupo' : $this->gruposBaja();
                break;
            case 'AlumnoGrupo' :
                $this->duplicaTable('alumnos_grupos');
                $this->truncateTables('alumnos_grupos');
                break;
            case 'Horario' :
                if (isset(DB::table('horarios')->orderBy('plantilla', 'desc')->first()->plantilla)) {
                    $this->plantilla = DB::table('horarios')->orderBy('plantilla', 'desc')->first()->plantilla;
                }
                else {
                    $this->plantilla = 0;
                }
                if ($xml == 'horarios_grupo') {
                    $this->truncateTables('horarios');
                }
                break;
            default: break;
        }
    }

    /**
     * @param $clase
     * @param $xml
     * @param Request $request
     */
    private function post($clase, $xml, $firstImport)
    {
        switch ($clase) {
            case 'Profesor' : $this->noSustituye();
                $this->asignaDepartamento();
                break;
            case 'Alumno': $this->bajaAlumnos();
                break;
            case 'Grupo' : if ($firstImport) { $this->bajaGrupos();  }
                $this->removeTutor();
                break;
            case 'AlumnoGrupo' : $this->eliminarRegistrosBlanco('alumnos_grupos', 'idGrupo');
                $this->restauraCopia();
                break;
            case 'Horario' : if ($xml == 'horarios_ocupaciones') {
                    $this->eliminarHorarios();
                    if ($firstImport) {
                        $this->crea_modulosCiclos();
                    }
                }
                break;
            default: break;
        }
    }

    /**
     * @return string
     */
    private static function getLinkSchedule(){
        if (Storage::exists('public/programacions.txt')) {
            $fichero = explode("\n", Storage::get('public/programacions.txt'));
            $indice = Modulo_ciclo::max('id') ? Modulo_ciclo::max('id') : 0;
            return $fichero[$indice];
        }
        return '';
    }

    /**
     * @return mixed
     */
    private static function getHoraris(){
        return Horario::distinct()->whereNotNull('idGrupo')
            ->whereNotNull('modulo')->whereNotNull('idProfesor')
            ->whereNotIn('modulo', config('constants.modulosSinProgramacion'))->get();
    }

    /**
     *
     */
    private static function newModuloCiclo($horario){
        $mc = new Modulo_ciclo();
        $mc->idModulo = $horario->modulo;
        $mc->idCiclo = $horario->Grupo->idCiclo;
        $mc->curso = substr($horario->idGrupo, 0, 1);
        $mc->idDepartamento = isset(Profesor::find($horario->idProfesor)->departamento) ? Profesor::find($horario->idProfesor)->departamento : '99';
        $mc->enlace = self::getLinkSchedule();
        $mc->save();
        return $mc;
    }
    /**
     * @param $mc
     * @param $horario
     */
    private static function newModuloGrupo($mc, $grupo)
    {
        $nuevo = new Modulo_grupo();
        $nuevo->idModuloCiclo = $mc;
        $nuevo->idGrupo = $grupo;
        $nuevo->save();
        return $nuevo;

    }

    /**
     * @param $mc
     */
    function newProgramacion($mc,$idProfesor)
    {
        $prg = New Programacion();
        $prg->idModuloCiclo = $mc->id;
        $prg->fichero = $mc->enlace;
        $prg->curso = Curso();

        if ($antigua = Programacion::where('idModuloCiclo', $mc->id)->first()) {
            $prg->criterios = $antigua->criterios;
            $prg->metodologia = $antigua->metodologia;
            $prg->propuestas = $antigua->propuestas;

        }

        $prg->save();
        return $prg;

    }

    /**
     *
     */
    private function crea_modulosCiclos()
    {
        foreach (self::getHoraris() as $horario){
            if (isset($horario->Grupo->idCiclo)) {
                if (! $mc = Modulo_ciclo::where('idModulo', $horario->modulo)->where('idCiclo', $horario->Grupo->idCiclo)->first() ){
                    $mc = self::newModuloCiclo($horario);
                }
                else
                    if ((isset(Profesor::find($horario->idProfesor)->departamento)) && ($mc->idDepartamento != Profesor::find($horario->idProfesor)->departamento)) {
                        $mc->idDepartamento = Profesor::find($horario->idProfesor)->departamento;
                        $mc->save();
                    }
                if (Modulo_grupo::where('idModuloCiclo', $mc->id)->where('idGrupo', $horario->idGrupo)->count() == 0){
                    self::newModuloGrupo($mc->id, $horario->idGrupo);
                }
                if (!Programacion::where('idModuloCiclo', $mc->id)->where('curso', Curso())->first()){
                    self::newProgramacion($mc,$horario->idProfesor);
                }
            } else {
                Alert::danger($horario->Grupo->id.' sin ciclo');
            }
        }


    }


/**
 *
 */
    private function alumnosBaja()
    {
        $hoy = Hoy();
        DB::table('alumnos')->whereNull('baja')->update(['baja' => $hoy]);
    }

    /**
     *
     */
    private function noSustituye()
    {
        foreach (Profesor::where('sustituye_a', '>', ' ')->get() as $sustituto) {
            $sustituido = Profesor::find($sustituto->sustituye_a);
            if ($sustituido && $sustituido->fecha_baja == NULL) {
                $sustituto->sustituye_a = '';
                $sustituto->save();
            }
        }
    }

    private function asignaDepartamento()
    {
        foreach (Profesor::where('departamento', 99)->get() as $profesor) {
            $horario = Horario::where('idProfesor',$profesor->dni)->whereNull('ocupacion')->where('modulo','!=','TU02CF')
                ->where('modulo','!=','TU01CF')->first();
            dd($profesor);
            if ($horario) {
                $modulo = Modulo_ciclo::where('idModulo',$horario->modulo)->first();
                if ($modulo) {
                    $profesor->departamento = $modulo->idDepartamento;
                    $profesor->save();
                }

            }
        }
    }

    /**
     *
     */
    private function profesoresBaja()
    {
        DB::table('profesores')->update(['activo' => false, 'sustituye_a' => '']);
    }

    /**
     *
     */
    private function bajaAlumnos()
    {
        DB::table('alumnos_grupos')->join('alumnos', 'idAlumno', '=', 'nia')->whereNotNull('alumnos.baja')->delete();
    }

    /**
     *
     */
    private function gruposBaja()
    {
        DB::table('grupos')->update(['tutor' => 'BAJA']);
    }

    /**
     *
     */
    private function bajaGrupos()
    {
        DB::table('grupos')->where('tutor', '=', 'BAJA')->delete();
    }

    /**
     *
     */
    private function removeTutor()
    {
         DB::table('grupos')->where('tutor','=',' ')->update(['tutor' => 'SIN TUTOR']);
    }

    /**
     * @param $fecha
     * @return string|null
     */
    private function getFechaFormatoIngles($fecha)
    {
        $fecha = str_replace('/', '-', $fecha);
        $fecha2 = date_create_from_format('j-m-Y', $fecha);
        if (!$fecha2) {
            return null;
        } else {
            return $fecha2->format('Y-m-d');
        }
    }

    /**
     * @param $cadena
     * @return string
     */
    private function cifrar($cadena)
    {
        return bcrypt(trim($cadena));
    }

    /**
     * @return int
     */
    private function crea_codigo_profesor()
    {
        $tots = 1;
        do {
            $azar = rand(1050, 9000);
            $tots = Profesor::where('codigo', $azar)->get()->count();
        } while ($tots > 0);
        return($azar);
    }

    /**
     * @param $telefono
     * @return bool|string
     */
    private function digitos($telefono)
    {
        return substr($telefono, 0, 9);
    }

    /**
     * @param $tipo_via
     * @param $domicilio
     * @param $numero
     * @param $puerta
     * @param $escalera
     * @param $letra
     * @param $piso
     * @return string
     */
    private function hazDomicilio($tipo_via, $domicilio, $numero, $puerta, $escalera, $letra, $piso)
    {
        $tipo_via = ($tipo_via == null) ? "" : trim($tipo_via);
        $domicilio = ($domicilio == null) ? "" : trim($domicilio);
        $numero = ($numero == null) ? "" : trim($numero);
        $puerta = ($puerta == null) ? "" : trim($puerta);
        $escalera = ($escalera == null) ? "" : trim($escalera);
        $letra = ($letra == null) ? "" : trim($letra);
        $piso = ($piso == null) ? "" : trim($piso);
        $domic = $tipo_via . " " . $domicilio . ", " . $numero;
        if ($puerta != "") {
            $domic .= " pta." . $puerta;
        }
        if ($escalera != "") {
            $domic .= " esc." . $escalera;
        }
        if ($piso != "") {
            $domic .= " " . $piso . "º";
        }
        if ($letra != "") {
            $domic .= "-" . $letra;
        }
        return ($domic);
    }

    /**
     * @param $atrxml
     * @param $llave
     * @param int $func
     * @return false|mixed|string|string[]|null
     */
    private function saca_campos($atrxml, $llave, $func = 1)
    {
        $lista = explode(",", $llave, 99);
        if (count($lista) == 1) {
            if (isset($atrxml[$llave])) {
                return (mb_convert_encoding($atrxml[$llave], 'utf8'));
            }
            else {
                return ($llave);
            }
        }
        else {
            for ($i = $func; $i < count($lista); $i++) {
                $params[$i - $func] = mb_convert_encoding($atrxml[$lista[$i]], 'utf8');
            }
            if ($func) {
                return (call_user_func_array(array($this, $lista[0]), $params));
            }
            else {
                return ($params);
            }
        }
    }

    /**
     * @param $filtro
     * @param $campos
     * @return bool
     */
    private function filtro($filtro, $campos)
    {
        $elemento = $campos[$filtro[0]];
        $op = $filtro[1];
        $valor = $filtro[2];
        $condicion = "return('$elemento' $op '$valor');";
        return eval($condicion) ? true : false;
    }
    /**
     * @param $required
     * @param $campos
     * @return bool
     */
    private function required($required, $campos)
    {
        $pasa = true;
        foreach ($required as $key) {
            if ($campos[$key] == ' ') {
                $campBuid = $key;
                $pasa = false;
            }
        }
        if (!$pasa) {
            Alert::danger("Camp $campBuid buid");
        }
        return $pasa;
    }

    /**
     * @param $xmltable
     * @param $tabla
     */
    private function in($xmltable, $tabla)
    {
        $guard = "\Intranet\Entities\\" . $tabla['nombreclase'] . '::unguard';
        $pt = call_user_func($guard);
        $pasa = true;
        foreach ($xmltable->children() as $registroxml) {  //recorro registro del xml
            $atributosxml = $registroxml->attributes(); // saco los valores de los atributos xml
            if (isset($tabla['filtro'])) {
                $pasa = $this->filtro($tabla['filtro'], $atributosxml);
            }
            if (isset($tabla['required'])) {
                $pasa = $pasa && $this->required($tabla['required'], $atributosxml);
            }
            if ($pasa) {
                $clase = "\Intranet\Entities\\" . $tabla['nombreclase']; //busco si ya existe en la bd
                $clave = $this->saca_campos($atributosxml, $tabla['id'], 0);

                if ($pt = $this->encuentra($clase, $clave)) {   //Update
                    foreach ($tabla['update'] as $keybd => $keyxml) {
                        $pt->$keybd = $this->saca_campos($atributosxml, $keyxml);
                    }
                    $pt->save();
                } else {  //create
                    if (isset($arrayDatos)) {
                        //borra el array de carga cada vez que entro bucle
                        unset($arrayDatos);
                    }
                    foreach ($tabla['update'] + $tabla['create'] as $keybd => $keyxml) {
                        $arrayDatos[$keybd] = $this->saca_campos($atributosxml, $keyxml);
                    }
                    try {
                        switch ($tabla['nombreclase']) {
                            case 'Horario':
                                if ($arrayDatos['plantilla'] >= $this->plantilla) {
                                    $this->plantilla = $arrayDatos['plantilla'];
                                    try {
                                        Horario::create($arrayDatos);
                                    } catch (\Illuminate\Database\QueryException $e) {
                                        unset($arrayDatos['aula']);
                                        Horario::create($arrayDatos);
                                    }
                                }
                                break;
                            case 'Alumno':
                                Alumno::create($arrayDatos);
                                break;
                            case 'Profesor':
                                Profesor::create($arrayDatos);
                                break;
                            case 'Modulo':
                                Modulo::create($arrayDatos);
                                break;
                            case 'Ocupacion':
                                Ocupacion::create($arrayDatos);
                                break;
                            case 'AlumnoGrupo':
                                AlumnoGrupo::create($arrayDatos);
                                break;
                            case 'Grupo':
                                Grupo::create($arrayDatos);
                                break;
                            case 'Espacio':
                                Espacio::create($arrayDatos);
                                break;
                            default: break;
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                        Alert::error($e->getMessage());
                        continue;
                    }
                }
            }
        }
        Alert::success($tabla['nombrexml'] . ' con ' . count($xmltable->children()) . ' Registres');
    }

    /**
     * @param $clase
     * @param $clave
     * @return mixed
     */
    private function encuentra($clase, $clave)
    {
        return $clase::find($clave);
    }

    /**
     * @param $tables
     */
    private function truncateTables($tables)
    {
        $this->checkForeignKeys(false);
        if (is_array($tables)) {
            foreach ($tables as $tabla) {
                DB::table($tabla)->truncate();
            }
        } else {
            DB::table($tables)->truncate();
        }
        $this->checkForeignKeys(true);
    }

    private function duplicaTable($table)
    {
        DB::statement("DROP table IF exists tmp_$table;");
        DB::statement("CREATE TABLE tmp_$table LIKE $table;");
        DB::statement("INSERT INTO tmp_$table SELECT * FROM $table;");
    }

    /**
     * @param $table
     * @param $columna
     */
    private function eliminarRegistrosBlanco($table, $columna)
    {
        DB::table($table)->where($columna, '=', '')->delete();
    }

    private function restauraCopia(){
        $tmpAll = DB::select("select * from tmp_alumnos_grupos where subGrupo IS NOT NULL");
        foreach ($tmpAll as $registro){
            $find = AlumnoGrupo::where('idAlumno',$registro->idAlumno)
                ->where('idGrupo',$registro->idGrupo)->first();
            if ($find) {
                $find->subGrupo = $registro->subGrupo;
                $find->posicion = $registro->posicion;
                $find->save();
            }
        }
        DB::statement('DROP table IF exists tmp_alumnos_grupos');
    }

    /**
     *
     */
    private function eliminarHorarios()
    {
        $ultimoHorario =  DB::table('horarios')->orderBy('plantilla', 'desc')->first();
        if ($ultimoHorario){
            $ultimPlantilla =  $ultimoHorario->plantilla;
            DB::table('horarios')->where('plantilla', '<>',$ultimPlantilla)->delete();
        }


    }

    /**
     * @param $check
     */
    private function checkForeignKeys($check)
    {
        $check = $check ? '1' : '0';
        DB::statement("SET FOREIGN_KEY_CHECKS= $check;");
    }

}
