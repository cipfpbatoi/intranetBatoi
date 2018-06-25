<?php
namespace Intranet\Http\Controllers;
use Illuminate\Http\Request;
Use Intranet\Entities\Alumno;
Use Intranet\Entities\Profesor;
use Intranet\Entities\Horario;
use Intranet\Entities\Alumno_grupo;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use DB;
use Illuminate\Database\Seeder;
use ImportTableSeeder;
use Illuminate\Support\Facades\Artisan;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Programacion;
class ImportController extends Seeder
{
    private $plantilla;
    
    private $campos_bd_xml = array(
        array('nombrexml' => 'alumnos',
            'nombreclase' => 'Alumno',
            'id' => 'NIA',
            'filtro' => ['estado_matricula', '<>', 'B'],
            'update' => array(
                'dni' => 'documento',
                'nombre' => 'nombre',
                'apellido1' => 'apellido1',
                'apellido2' => 'apellido2',
                'email' => 'email1',
                'fecha_nac' => 'getFechaFormatoIngles,fecha_nac',
                'sexo' => 'sexo',
                'expediente' => 'expediente',
                'domicilio' => 'hazDomicilio,tipo_via,domicilio,numero,puerta,escalera,letra,piso',
                'codigo_postal' => 'cod_postal',
                'provincia' => 'provincia',
                'municipio' => 'municipio',
                'telef1' => 'digitos,telefono1',
                'telef2' => 'telefono2',
                'fecha_ingreso' => 'getFechaFormatoIngles,fecha_ingreso_centro',
                'fecha_matricula' => 'getFechaFormatoIngles,fecha_matricula',
                'repite' => 'repite',
                'turno' => 'turno',
                'trabaja' => 'trabaja',
                'password' => 'cifrar,documento',
                'baja' => null
            ),
            'create' => array(
                'nia' => 'NIA',
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
                'movil2' => 'telefono2',
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
                'api_token' => 'aleatorio,0'
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
            'nombreclase' => 'Alumno_grupo',
            'filtro' => ['estado_matricula', '<>', 'B'],
            'id' => 'NIA,grupo',
            'update' => array(
                'idAlumno' => 'NIA',
                'idGrupo' => 'grupo'
            ),
            'create' => array(
            )),
        array('nombrexml' => 'aulas',
            'nombreclase' => 'Espacio',
            'id' => 'aula',
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
    public function create()
    {
        return view('seeder.create');
    }
    public function email($nombre, $apellido)
    {
        return strtolower(substr($nombre, 0, 1) . $apellido.'@'.config('contacto.host.dominio'));
    }
    public function aleatorio()
    {
        return str_random(60);
    }
    
    public function store(Request $request)
    {
        if ($request->hasFile('fichero')) {
            if ($request->file('fichero')->isValid()) {
                $extension = $request->file('fichero')->getClientOriginalExtension();
                if ($extension == 'xml') {
                    ini_set('max_execution_time', 360);
                    $this->run($request->file('fichero'), $request);
                    ini_set('max_execution_time', 30);
                } else
                    Alert::danger(trans('messages.generic.invalidFormat'));
            } else
                Alert::danger(trans('messages.generic.invalidFormat'));
        } else
            Alert::danger(trans('messages.generic.noFile'));
        foreach (Profesor::all() as $profesor) {
            if ($grupo = Grupo::QTutor($profesor->dni)->first()) {
                if (!esRol($profesor->rol, config('constants.rol.tutor'))) {
                    $profesor->rol *= config('constants.rol.tutor');
                    $profesor->save();
                    Alert::success('tutors assignat: ' . $profesor->FullName);
                }
                if ($grupo->curso == 2 && !esRol($profesor->rol, config('constants.rol.practicas'))) {
                    $profesor->rol *= config('constants.rol.practicas');
                    $profesor->save();
                    Alert::success('tutor practicas assignat: ' . $profesor->FullName);
                }
                if ($request->primera && $grupo->curso == 1 && esRol($profesor->rol, config('constants.rol.practicas'))) {
                    $profesor->rol /= config('constants.rol.practicas');
                    $profesor->save();
                    Alert::success('tutor practicas degradat: ' . $profesor->FullName);
                }
            } else {
                if (esRol($profesor->rol, config('constants.rol.tutor'))) {
                    $profesor->rol /= config('constants.rol.tutor');
                    $profesor->save();
                    Alert('tutor degradat' . $profesor->FullName);
                }
                if ($request->primera && esRol($profesor->rol, config('constants.rol.practicas'))) {
                    $profesor->rol /= config('constants.rol.praticas');
                    $profesor->save();
                    Alert('tutor practicas degradat' . $profesor->FullName);
                }
            }
        }
//        $this->empresas();
        return view('seeder.store', compact('result'));
    }
    // Ejecuta la obra de arte
    public function run($fxml, Request $request)
    {
        if (file_exists($fxml)) {
            $xml = simplexml_load_file($fxml);
            foreach ($this->campos_bd_xml as $tabla) {
                $xmltable = $xml->{$tabla['nombrexml']}; //miro en el xml que tengo de la tabla
                if (count($xmltable)) {
                    $this->pre($tabla['nombreclase'], $tabla['nombrexml']);
                    $this->in($xmltable, $tabla);
                    $this->post($tabla['nombreclase'], $tabla['nombrexml'], $request);
                } else
                    Alert::danger('No hay registros de ' . $tabla['nombrexml'] . ' en el xml');
            }
        } else
            Alert::danger(trans('messages.generic.noFile'));
    }
    private function pre($clase, $xml)
    {
        switch ($clase) {
            case 'Alumno': $this->alumnosBaja();
                break;
            case 'Profesor' : $this->profesoresBaja();
                break;
            case 'Grupo' : $this->gruposBaja();
                break;
            case 'Alumno_grupo' : $this->truncateTables('alumnos_grupos');
                break;
            case 'Horario' : 
                    if (isset(DB::table('horarios')->orderBy('plantilla', 'desc')->first()->plantilla)) 
                       $this->plantilla = DB::table('horarios')->orderBy('plantilla', 'desc')->first()->plantilla;
                    else
                        $this->plantilla = 0;  
                    if ($xml == 'horarios_grupo')  $this->truncateTables('horarios');
                break;
        }
    }
    private function post($clase, $xml, Request $request)
    {
        switch ($clase) {
            case 'Profesor' : $this->noSustituye();
                break;
            case 'Alumno': $this->bajaAlumnos();
                break;
            case 'Grupo' : if ($request->primera) $this->bajaGrupos();
                break;
            case 'Alumno_grupo' : $this->eliminarRegistrosBlanco('alumnos_grupos', 'idGrupo');
                break;
            case 'Horario' : if ($xml == 'horarios_ocupaciones') {
                    $this->eliminarHorarios();
                    if ($request->primera) $this->crea_modulosCiclos();
                }
                break;
        }
    }
    private function crea_modulosCiclos()
    {
        $enlace = (Storage::exists('public/programacions.txt')) ? true : false;
        if ($enlace) {
            $fichero = explode("\n", Storage::get('public/programacions.txt'));
            $indice = Modulo_ciclo::max('id') ? Modulo_ciclo::max('id') : 0;
        }
        $horarios = Horario::distinct()->whereNotNull('idGrupo')
                        ->whereNotNull('modulo')->whereNotNull('idProfesor')
                        ->whereNotIn('modulo', config('constants.modulosNoLectivos'))->get();
        foreach ($horarios as $horario) {
            if (isset($horario->Grupo->idCiclo)) {
                if (Modulo_ciclo::where('idModulo', $horario->modulo)->where('idCiclo', $horario->Grupo->idCiclo)->count() == 0) {
                    $nuevo = new Modulo_ciclo();
                    $nuevo->idModulo = $horario->modulo;
                    $nuevo->idCiclo = $horario->Grupo->idCiclo;
                    $nuevo->curso = substr($horario->idGrupo, 0, 1);
                    $nuevo->idDepartamento = isset(Profesor::find($horario->idProfesor)->departamento) ? Profesor::find($horario->idProfesor)->departamento : '';
                    if ($enlace)
                        $nuevo->enlace = $fichero[$indice++];
                    $nuevo->save();
                }
                else {
                    $nuevo = Modulo_ciclo::where('idModulo', $horario->modulo)->where('idCiclo', $horario->Grupo->idCiclo)->first();
                    if ((isset(Profesor::find($horario->idProfesor)->departamento)) && ($nuevo->idDepartamento != Profesor::find($horario->idProfesor)->departamento)) {
                        $nuevo->idDepartamento = Profesor::find($horario->idProfesor)->departamento;
                        $nuevo->save();
                    }
                }
                $mc = Modulo_ciclo::where('idModulo', $horario->modulo)->where('idCiclo', $horario->Grupo->idCiclo)->first();
                if (Modulo_grupo::where('idModuloCiclo', $mc->id)->where('idGrupo', $horario->idGrupo)->count() == 0) {
                    $nuevo = new Modulo_grupo();
                    $nuevo->idModuloCiclo = $mc->id;
                    $nuevo->idGrupo = $horario->idGrupo;
                    $nuevo->save();
                }
                if (!Programacion::where('idModuloCiclo', $mc->id)->where('curso', Curso())->first()) {
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
                }
            }
        }
    }
    private function alumnosBaja()
    {
        $hoy = Hoy();
        DB::table('alumnos')->whereNull('baja')->update(['baja' => $hoy]);
    }
    private function noSustituye()
    {
        $sustitutos = Profesor::where('sustituye_a', '>', ' ')->get();
        foreach ($sustitutos as $sustituto) {
            $sustituido = Profesor::find($sustituto->sustituye_a);
            if ($sustituido && $sustituido->fecha_baja == NULL) {
                $sustituto->sustituye_a = '';
                $sustituto->save();
            }
        }
    }
    private function profesoresBaja()
    {
        DB::table('profesores')->update(['activo' => false, 'sustituye_a' => '']);
    }
    private function bajaAlumnos()
    {
        DB::table('alumnos_grupos')->join('alumnos', 'idAlumno', '=', 'nia')->whereNotNull('alumnos.baja')->delete();
    }
    private function gruposBaja()
    {
        DB::table('grupos')->update(['tutor' => 'BAJA']);
    }
    private function bajaGrupos()
    {
        DB::table('grupos')->where('tutor', '=', 'BAJA')->delete();
    }
    private function getFechaFormatoIngles($fecha)
    {
        $fecha = str_replace('/', '-', $fecha);
        $fecha2 = date_create_from_format('j-m-Y', $fecha);
        //var_dump($fecha2);
        if (!$fecha2) {
            return null;
        } else {
            return $fecha2->format('Y-m-d');
        }
    }
    // Cifrar cadena
    private function cifrar($cadena)
    {
        return bcrypt(trim($cadena));
        //return crypt($cadena, $this->hash);
    }
    // Crea codigo que no esté
    private function crea_codigo_profesor()
    {
        $tots = 1;
        do {
            $azar = rand(1050, 9000);
            $tots = Profesor::where('codigo', $azar)->get()->count();
        } while ($tots > 0);
        return($azar);
    }
    private function digitos($telefono)
    {
        return substr($telefono, 0, 9);
    }
    // Construye domicilio
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
        if ($puerta != "")
            $domic .= " pta." . $puerta;
        if ($escalera != "")
            $domic .= " esc." . $escalera;
        if ($piso != "")
            $domic .= " " . $piso . "º";
        if ($letra != "")
            $domic .= "-" . $letra;
        return ($domic);
    }
    
    private function saca_campos($atrxml, $llave, $func = 1)
    {
        $lista = explode(",", $llave, 99);
        if (count($lista) == 1) {
            if (isset($atrxml[$llave]))
                return(mb_convert_encoding($atrxml[$llave],'utf8'));
            else
                return($llave);
        }
        else {
            for ($i = $func; $i < count($lista); $i++) {
                $params[$i - $func] = mb_convert_encoding($atrxml[$lista[$i]],'utf8');
            }
            if ($func)
                return (call_user_func_array(array($this, $lista[0]), $params));
            else
                return ($params);
        }
    }
    
    private function filtro($filtro, $campos)
    {
        $elemento = $campos[$filtro[0]];
        $op = $filtro[1];
        $valor = $filtro[2];
        $condicion = "return('$elemento' $op '$valor');";
        return eval($condicion) ? true : false;
    }
    private function in($xmltable, $tabla)
    {
        $guard = "\Intranet\Entities\\" . $tabla['nombreclase'] . '::unguard';
        $pt = call_user_func($guard);
        $pasa = true;
        foreach ($xmltable->children() as $registroxml) {  //recorro registro del xml
            $atributosxml = $registroxml->attributes(); // saco los valores de los atributos xml
            if (isset($tabla['filtro'])) $pasa = $this->filtro($tabla['filtro'], $atributosxml);
            if ($pasa)
                    {
                $clase = "\Intranet\Entities\\" . $tabla['nombreclase']; //busco si ya existe en la bd
                $clave = $this->saca_campos($atributosxml, $tabla['id'], 0);
           
                if ($pt = $this->encuentra($clase, $clave)) {   //Update
                    foreach ($tabla['update'] as $keybd => $keyxml) {
                        $pt->$keybd = $this->saca_campos($atributosxml, $keyxml);
                    }
                    $pt->save();
                } else {  //create
                    if (isset($arrayDatos)) unset($arrayDatos); //borra el array de carga cada vez que entro bucle
                    foreach ($tabla['update'] + $tabla['create'] as $keybd => $keyxml) {
                        $arrayDatos[$keybd] = $this->saca_campos($atributosxml, $keyxml);
                    }
                    $create = "\Intranet\Entities\\" . $tabla['nombreclase'] . '::create';
                    switch ($tabla['nombreclase']) {
                        case 'Horario':
                            if ($arrayDatos['plantilla']>= $this->plantilla)
                                Horario::create($arrayDatos);
                            break;
                        case 'Alumno':
                            Alumno::create($arrayDatos);
                            break;
                        case 'Profesor':
                            Profesor::create($arrayDatos);
                            break;
                        case 'Modulo':
                            dd('hola');
                            Modulo::create($arrayDatos);
                            break;
                        case 'Ocupacion':
                            Ocupacion::create($arrayDatos);
                            break;
                        case 'Alumno_grupo':
                            Alumno_grupo::create($arrayDatos);
                            break;
                        case 'Grupo':
                            Grupo::create($arrayDatos);
                            break;
//                        default:
//                            $pt = call_user_func($create, $arrayDatos);
//                            break;
                    }
                }
            }
        }
        Alert::success($tabla['nombrexml'] . ' con ' . count($xmltable->children()) . ' Registres');
    }
    private function encuentra($clase, $clave)
    {
        return $clase::find($clave);
    }
    private function truncateTables($tables)
    {
        $this->checkForeignKeys(false);
        if (is_array($tables))
            foreach ($tables as $tabla) {
                DB::table($tabla)->truncate();
            } else
            DB::table($tables)->truncate();
        $this->checkForeignKeys(true);
    }
    private function eliminarRegistrosBlanco($table, $columna)
    {
        DB::table($table)->where($columna, '=', '')->delete();
    }
    private function eliminarHorarios()
    {
        $maxHorarios = DB::table('horarios')->orderBy('plantilla', 'desc')->first()->plantilla;
        DB::table('horarios')->where('plantilla', '<>', $maxHorarios)->delete();
//        $maxHorarios = DB::table('horarios')->whereNotNull('ocupacion')->orderBy('plantilla', 'desc')->first()->plantilla;
//        DB::table('horarios')->whereNotNull('ocupacion')->where('plantilla', '<>', $maxHorarios)->delete();
    }
    private function checkForeignKeys($check)
    {
        $check = $check ? '1' : '0';
        DB::statement("SET FOREIGN_KEY_CHECKS= $check;");
    }
}