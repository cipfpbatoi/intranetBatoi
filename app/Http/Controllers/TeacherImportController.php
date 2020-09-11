<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
use ImportTableSeeder;
use Illuminate\Support\Facades\Artisan;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Programacion;
use Intranet\Entities\Ocupacion;

/**
 * Class ImportController
 * @package Intranet\Http\Controllers
 */
class TeacherImportController extends Seeder
{

    /**
     * @var
     */
    private $plantilla;
    /**
     * @var array
     */
    private $campos_bd_xml = array(
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
                'api_token' => 'aleatorio,60'
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
        return view('seeder.createTeacher');
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

        return view('seeder.store');
    }


    /**
     * @param $fxml
     * @param Request $request
     */
    public function run($fxml, Request $request)
    {
        $xml = simplexml_load_file($fxml);
        if ($request->horari) $this->esborraHoraris($request->idProfesor);
        foreach ($this->campos_bd_xml as $table)
            $this->manageTable($xml->{$table['nombrexml']},$table,$request->idProfesor);

    }

    /**
     * @param $xmltable
     * @param $table
     * @param $firstImport
     */
    private function manageTable($xmltable, $table, $idProfesor){
        if (count($xmltable))
            $this->import($xmltable, $table, $idProfesor);
        else
            Alert::danger('No hay registros de ' . $table['nombrexml'] . ' en el xml');
    }

    /**
     * @param $clase
     * @param $xml
     */
    private function esborraHoraris($idProfesor)
    {
        if (isset(DB::table('horarios')->orderBy('plantilla', 'desc')->first()->plantilla))
            $this->plantilla = DB::table('horarios')->orderBy('plantilla', 'desc')->first()->plantilla;
        else
            $this->plantilla = 0;
        DB::table('horarios')->where('idProfesor',$idProfesor)->delete();

    }

    /**
     * @param $fecha
     * @return string|null
     */
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

    /**
     * @param $cadena
     * @return string
     */
    private function cifrar($cadena)
    {
        return bcrypt(trim($cadena));
        //return crypt($cadena, $this->hash);
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
            if (isset($atrxml[$llave]))
                return(mb_convert_encoding($atrxml[$llave], 'utf8'));
            else
                return($llave);
        }
        else {
            for ($i = $func; $i < count($lista); $i++) {
                $params[$i - $func] = mb_convert_encoding($atrxml[$lista[$i]], 'utf8');
            }
            if ($func)
                return (call_user_func_array(array($this, $lista[0]), $params));
            else
                return ($params);
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
            if ($campos[$key] == ' ')
                $pasa = false;
        }
        if (!$pasa)
            Alert::danger('Camp buid: ' . print_r($campos, true));
        return $pasa;
    }

    /**
     * @param $xmltable
     * @param $tabla
     */
    private function import($xmltable, $tabla,$idProfesor)
    {
        $guard = "\Intranet\Entities\\" . $tabla['nombreclase'] . '::unguard';
        $pt = call_user_func($guard);
        $pasa = true;
        foreach ($xmltable->children() as $registroxml) {  //recorro registro del xml
            $atributosxml = $registroxml->attributes(); // saco los valores de los atributos xml
            if (isset($tabla['filtro']))
                $pasa = $this->filtro($tabla['filtro'], $atributosxml);
            if (isset($tabla['required']))
                $pasa = $pasa && $this->required($tabla['required'], $atributosxml);
            if ($pasa) {
                $clase = "\Intranet\Entities\\" . $tabla['nombreclase']; //busco si ya existe en la bd
                $clave = $this->saca_campos($atributosxml, $tabla['id'], 0);

                if ($pt = $this->encuentra($clase, $clave)) {   //Update
                    foreach ($tabla['update'] as $keybd => $keyxml) {
                        $pt->$keybd = $this->saca_campos($atributosxml, $keyxml);
                    }
                    $pt->save();
                } else {  //create
                    if (isset($arrayDatos))
                        unset($arrayDatos); //borra el array de carga cada vez que entro bucle
                    foreach ($tabla['update'] + $tabla['create'] as $keybd => $keyxml) {
                        $arrayDatos[$keybd] = $this->saca_campos($atributosxml, $keyxml);
                    }
                    $create = "\Intranet\Entities\\" . $tabla['nombreclase'] . '::create';

                    try {
                        switch ($tabla['nombreclase']) {
                            case 'Horario':
                                if ($arrayDatos['plantilla'] >= $this->plantilla && $arrayDatos['idProfesor']==$idProfesor) {
                                    $this->plantilla = $arrayDatos['plantilla'];
                                    try {
                                        Horario::create($arrayDatos);
                                    } catch (\Illuminate\Database\QueryException $e) {
                                        unset($arrayDatos['aula']);
                                        Horario::create($arrayDatos);
                                    }
                                }
                                break;
                            case 'Profesor':
                                if ($arrayDatos['dni']==$idProfesor)
                                Profesor::create($arrayDatos);
                                break;
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
        if (is_array($tables))
            foreach ($tables as $tabla) {
                DB::table($tabla)->truncate();
            } else
            DB::table($tables)->truncate();
        $this->checkForeignKeys(true);
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
