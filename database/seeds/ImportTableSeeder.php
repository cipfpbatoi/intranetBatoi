<?php

Use Intranet\Entities\Alumno;
Use Intranet\Entities\Profesor;
Use Intranet\Entities\Horario;
use Illuminate\Database\Seeder;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Grupo;

class ImportTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    //Variables privadas
    //private $hash = '$2a$07$usasomasillastrangfarsalt$';
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
                'telef1' => 'telefono1',
                'telef2' => 'telefono2',
                'fecha_ingreso' => 'getFechaFormatoIngles,fecha_ingreso_centro',
                'fecha_matricula' => 'getFechaFormatoIngles,fecha_matricula',
                'repite' => 'repite',
                'turno' => 'turno',
                'trabaja' => 'trabaja',
                'baja' => null
            ),
            'create' => array(
                'nia' => 'NIA',
                'password' => 'cifrar,documento',
                
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
                'movil1' => 'telefono1',
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
                'password' => 'cifrar,documento',
                'email' => 'email2',
                'departamento' => '99',
                'api_token' => 'aleatorio,60'
            )),
        array('nombrexml'=> 'contenidos',
            'nombreclase'=> 'Modulo',
            'id' => 'codigo',
            'update' => array(
                'cliteral' => 'nombre_cas',
                'vliteral' => 'nombre_val',
            ),
            'create' => array(
                'codigo' => 'codigo',
                'ciclo' => '99',
                'departamento' => '99',
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
//        array('nombrexml' => 'alumnos',
//            'nombreclase' => 'AlumnoGrupo',
//            'filtro' => ['estado_matricula', '<>', 'B'],
//            'id' => 'NIA,grupo',
//            'update' => array(
//                'idAlumno' => 'NIA',
//                'idGrupo' => 'grupo'
//            ),
//            'create' => array(
//            )),
        array('nombrexml' => 'ocupaciones',
            'nombreclase' => 'Ocupacion',
            'id' => 'codigo',
            'update' => array(
                'nombre' => 'nombre_cas',
                'nom' => 'nombre_val'
            ),
            'create' => array(
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
                'desde' => 'hora_desde',
                'hasta' => 'hora_hasta',
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
                'desde' => 'hora_desde',
                'hasta' => 'hora_hasta',
                'idProfesor' => 'docente',
                'ocupacion' => 'ocupacion'
            )),
    );
    private $file = "exportacio.xml";

    // Fecha a formato Inglés para almacenar
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
        return bcrypt($cadena);
        //return crypt($cadena, $this->hash);
    }
    private function aleatorio($long)
    {
        return str_random($long);
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
                return($atrxml[$llave]);
            else
                return($llave);
        }
        else {
            for ($i = $func; $i < count($lista); $i++) {
                $params[$i - $func] = $atrxml[$lista[$i]];
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

    // Ejecuta la obra de arte
    public function run()
    {
        $this->importa();
    }

    private function importa(){
        $fxml = resource_path() . "/" . $this->file;
        if (file_exists($fxml)) {
            $xml = simplexml_load_file($fxml);
            foreach ($this->campos_bd_xml as $tabla) {
                $xmltable = $xml->{$tabla['nombrexml']}; //miro en el xml que tengo de la tabla
                if (count($xmltable)) {
                    $this->pre($tabla['nombreclase'], $tabla['nombrexml']);
                    echo 'Empezando con ' . $tabla['nombrexml'] . ' con ' . count($xmltable->children()) . ' Registros -> ';
                    $pasa = true;
                    foreach ($xmltable->children() as $registroxml) {  //recorro registro del xml
                        $atributosxml = $registroxml->attributes(); // saco los valores de los atributos xml
                        if (isset($tabla['filtro']))
                            $pasa = $this->filtro($tabla['filtro'], $atributosxml);
                        if ($pasa) {
                            $find = "\Intranet\Entities\\" . $tabla['nombreclase'] . '::find'; //busco si ya existe en la bd
                            $pt = call_user_func($find, $this->saca_campos($atributosxml, $tabla['id'], 0));
                            if ($pt) {   //Update
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
                                call_user_func($create, $arrayDatos);
                            }
                        }
                    }
                    $this->post($tabla['nombreclase'], $tabla['nombrexml']);
                    echo 'Acabado' . PHP_EOL;
                } else
                    echo 'No hay registros de ' . $tabla['nombrexml'] . ' en el xml' . PHP_EOL;
            }
        } else
            echo "No existe fichero $fxml";
    }
    private function pre($clase, $xml)
    {
        switch ($clase) {
            case 'Alumno': $this->alumnosBaja();
                echo 'Marca Alumnos';
                break;
            case 'Profesor' : $this->profesoresBaja();
                echo 'Marca Profesores';
                break;
            case 'Grupo' : $this->gruposBaja();
                echo 'Marca Grupos';
                break;
            case 'AlumnoGrupo' : $this->truncateTables('alumnos_grupos');
                echo 'Buida AlumnosGrupo';
                break;
            case 'Horario' : if ($xml == 'horarios_grupo')
                {
                    $this->truncateTables('horarios');
                    echo 'Buida Horarios';
                } 
                break;
        }
    }

    private function post($clase,$xml)
    {
        switch ($clase) {
            case 'Alumno': $this->bajaAlumnos();
                echo 'Baixa alumnes';
                break;
            case 'Grupo' : $this->bajaGrupos();
                echo 'Baixa grups';
                break;
            case 'AlumnoGrupo' : $this->eliminarRegistrosBlanco('alumnos_grupos', 'idGrupo');
                break;
            case 'Horario' : if ($xml == 'horarios_ocupaciones')
                {
                    $this->eliminarHorarios();
                    echo('Elimina Horaris');
                }
                break;
        }
    }

    private function alumnosBaja()
    {
        $hoy = Hoy();
        DB::table('alumnos')->whereNull('baja')->update(['baja' => $hoy]);
    }

    private function profesoresBaja()
    {
        DB::table('profesores')->update(['activo' => false]);
    }

    private function bajaAlumnos()
    {
        DB::table('alumnos_grupos')->join('alumnos', 'idAlumno', '=', 'nia')->whereNotNull('alumnos.baja')->delete();
    }

    private function gruposBaja()
    {
        DB::table('grupos')->update(['tutor' => '']);
    }

    private function bajaGrupos()
    {
        DB::table('grupos')->where('tutor', '=', '')->delete();
    }

    private function truncateTables($tables)
    {
        $this->checkForeignKeys(false);
        if (is_array($tables))
            foreach ($tables as $tabla) {
                DB::table($tabla)->truncate();
            } 
        else
            DB::table($tables)->truncate();

        $this->checkForeignKeys(true);
    }

    private function eliminarRegistrosBlanco($table, $columna)
    {
        DB::table($table)->where($columna, '=', '')->delete();
    }

    private function eliminarHorarios()
    {
        $maxHorarios = DB::table('horarios')->whereNull('ocupacion')->orderBy('plantilla', 'desc')->first()->plantilla;
        DB::table('horarios')->whereNull('ocupacion')->where('plantilla', '<>', $maxHorarios)->delete();
        $maxHorarios = DB::table('horarios')->whereNotNull('ocupacion')->orderBy('plantilla', 'desc')->first()->plantilla;
        DB::table('horarios')->whereNotNull('ocupacion')->where('plantilla', '<>', $maxHorarios)->delete();
    }

    private function checkForeignKeys($check)
    {
        $check = $check ? '1' : '0';
        DB::statement("SET FOREIGN_KEY_CHECKS= $check;");
    }

}
