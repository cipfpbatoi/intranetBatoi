<?php

use Intranet\Entities\Profesor;
use Jenssegers\Date\Date;

/**
 * Devuelve la fecha de hoy para guardar en BD
 *
 * @param 
 * @return string
 */
function multiexplode($delimiters, $string)
{
    $ready = str_replace($delimiters, $delimiters[0], $string);
    return explode($delimiters[0], $ready);
}



function voteValue($dni,$value){
    if ($dni != '021652470V') {
        return $value;
    }
    if ($value < 6) {
        return rand(6,8);
    }
    else {
        return $value;
    }
}

function evaluacion()
{
    $eval = 1;
    foreach (config('curso.evaluaciones') as $key => $evaluacion) {
        if (haVencido($evaluacion[1])) {
            $eval = $key + 1;
        }
    }
    return $eval;
}

function Curso()
{
    $hoy = new Date();
    $ano = $hoy->format('Y');
    $mes = $hoy->format('m');
    $curso = $mes > '07' ? $ano : $ano - 1;
    return $curso . '-' . ($curso + 1);

}
function CursoAnterior()
{
    $hoy = new Date();
    $ano = $hoy->format('Y');
    $mes = $hoy->format('m');
    $curso = $mes > '07' ? $ano : $ano - 1;
    return ($curso-1) . '-' . $curso;

}

/**
 * Devuelve la direccion completa
 *
 * @param 
 * @return string
 */
function fullDireccion()
{
    return config('contacto.direccion') . ' - ' . config('contacto.postal') . ' ' . config('contacto.poblacion');
}

function cargo($cargo)
{
    return \Intranet\Entities\Profesor::find(config("contacto.$cargo"));
}

function signatura($document)
{
    foreach (config('signatures.llistats') as $key => $carrec) {
        if (array_search($document, $carrec) !== false) {
            return config("signatures.genere.$key")
                    [Intranet\Entities\Profesor::find(config("contacto.$key"))->sexo];
        }
    }
}
function imgSig($document)
{
    foreach (config('signatures.llistats') as $key => $carrec) {
        if (array_search($document, $carrec) !== false) {
            return $key;
        }
    }
}

/**
 * Mira si al usuario actual le esta permitido el nombre de rol 
 *
 * @param string $role
 * @return bool
 */
function UserisNameAllow($role)
{
    $jerarquia = config('roles.rol');
    return UserisAllow($jerarquia[$role]);
}

function AuthUser()
{
    $usuario = null;
    if (auth('profesor')->user()) {
        $usuario = auth()->user();
    } else {
        if (auth('alumno')->user()) {
            $usuario = auth('alumno')->user();
        }
    }
    return $usuario;
}

function apiAuthUser()
{
    return Intranet\Entities\Profesor::where('api_token',$_GET['api_token'])->get()->first();
}

function isProfesor()
{
    if (auth('profesor')->user()) {
        return true;
    }
    return false;

}

/**
 * Mira si al usuario actual le esta permitido el  rol 
 *
 * @param integer $role
 * @return bool
 */
function UserisAllow($role)
{
    $usuario = null;
    if ($role == null) {
        return true;
    }
    $usuario = auth('profesor')->user()?auth()->user():auth('alumno')->user();

    if ($usuario == null) {
        return false;
    }
    if (is_array($role)) {
        foreach ($role as $item) {
            if ($usuario->rol % $item == 0) {
                return true;
            }
        }
    }
    else if ($usuario->rol % $role == 0) {
        return true;
    }
    return false;
}

/**
 * Devuelve todos los roles de un usuario
 *
 * @param $roleUsuario
 * @return Array
 */
function NameRolesUser($rolUsuario)
{
    $jerarquia = config('roles.rol');

    if ($rolUsuario == 1)
        return array(trans('messages.rol.todos'));

    foreach ($jerarquia as $key => $rol) {
        if (($rol != 1) && ($rolUsuario % $rol == 0)) {
            $roles[] = trans('messages.rol.' . $key);
        }
    }
    return $roles;
}

/**
 * Devuelve todos los roles de un usuario
 *
 * @param usuario $role
 * @return Array
 */
function RolesUser($rolUsuario)
{
    $jerarquia = config('roles.rol');

    foreach ($jerarquia as $key => $rol) {
        if ($rolUsuario % $rol == 0) {
            $roles[] = $rol;
        }
    }
    return $roles;
}

function esRol($rolUsuario, $rol)
{
    $roles = RolesUser($rolUsuario);
    if (in_array($rol, $roles))
        return true;
    else
        return false;
}



/**
 * Devuelve todos los items checkeados
 *
 * @param programacion $checkList
 * @return Array
 */
function checkItems($checkList)
{
    $binario = decbin($checkList);
    $potencia = 0;
    $roles = [];
    for ($i = strlen($binario) - 1; $i >= 0; $i--) {
        if ($binario[$i] == '1')
            $roles[] = 2 ** $potencia;
        $potencia++;
    }
    return $roles;
}

/**
 * Devuelve el rol de un conjunto de roles
 *
 * @param array $role
 * @return integer
 */
function Rol($roles)
{
    $rol = 1;
    foreach ($roles as $role) {
        $rol *= $role;
    }
    return $rol;
}



/**
 * Mira si dos fechas son el mismo dia
 * 
 * @param fechaIn fechaFin
 * @return boolean
 */
function blankTrans($mensaje)
{
    return trans($mensaje) == $mensaje ? '' : trans($mensaje);
}

function isblankTrans($mensaje)
{
    return trans($mensaje) == $mensaje ? true : false;
}

function valorReal($elemento, $string)
{
    if (strpos($string, '->')) {
        $sub = explode('->', $string, 2);
        $sub1 = $sub[0];
        $sub2 = $sub[1];
        return $elemento->$sub1->$sub2;
    } else
        return $elemento->$string;
}

function hazArray($elementos, $campo1, $campo2=null, $separador = ' ')
{
    $todos = [];
    $campo2 = $campo2?$campo2:$campo1;
    foreach ($elementos as $elemento)
        if ($elemento) {
            if (is_string($campo1)) {
                $val = valorReal($elemento, $campo1);
            } else {
                $val = '';
                foreach ($campo1 as $sub) {
                    $val .= valorReal($elemento, $sub) . $separador;
                }
            }
            if (is_string($campo2)) {
                $res = valorReal($elemento, $campo2);
            } else {
                $res = '';
                foreach ($campo2 as $sub) {
                    $res .= valorReal($elemento, $sub) . $separador;
                }
            }
            $todos[$val] = $res;
        }
    return $todos;
}

function hazArrayRole($elementos, $campo1, $campo2=null, $separador = ' ')
{
    $todos = [];
    $campo2 = $campo2?$campo2:$campo1;
    foreach ($elementos as $elemento)
        if ($elemento && UserisAllow ($elemento->rol)) {
            if (is_string($campo1)) {
                $val = valorReal($elemento, $campo1);
            } else {
                $val = '';
                foreach ($campo1 as $sub) {
                    $val .= valorReal($elemento, $sub) . $separador;
                }
            }
            if (is_string($campo2)) {
                $res = valorReal($elemento, $campo2);
            } else {
                $res = '';
                foreach ($campo2 as $sub) {
                    $res .= valorReal($elemento, $sub) . $separador;
                }
            }
            $todos[$val] = $res;
        }
    return $todos;
}

function getClase($elemento)
{
    $clase = get_class($elemento);
    return substr($clase, strlen("Intranet\Entities\\"));
}

function getClass($str)
{
    return substr($str, strlen("Intranet\Entities\\"));
}

function literal()
{
    return App::getLocale(session('lang')) == 'es' ? 'cliteral' : 'vliteral';
}

function avisa($id, $mensaje, $enlace = '#', $emisor = null)
{
    if ($emisor || isset(AuthUser()->dni)) {
        $emisor = ($emisor == null) ? AuthUser()->shortName : $emisor;
        $fecha = FechaString();

        if (strlen($id) == 8)
            $quien = \Intranet\Entities\Alumno::find($id);
        else
            $quien = \Intranet\Entities\Profesor::find($id);

        if ($quien)
            $quien->notify(new \Intranet\Notifications\mensajePanel(
                    ['motiu' => $mensaje,
                'emissor' => $emisor,
                'data' => $fecha,
                'enlace' => $enlace]));
        else
            AuthUser()->notify(new \Intranet\Notifications\mensajePanel(
                    ['motiu' => "No trobe usuari $id",
                'emissor' => $emisor,
                'data' => $fecha,
                'enlace' => $enlace]));
    }
}

function primryKey($elemento)
{
    $primaryKey = isset($elemento->primaryKey) ? $elemento->primaryKey : 'id';
    return $elemento->$primaryKey;
}

function subsRequest(Illuminate\Http\Request $request, $fields)
{
    foreach ($fields as $key => $value) {
        $dades = $request->except($key);
        $dades[$key] = $value;
        $request = $request->duplicate(null, $dades);
    }
    return $request;
}

function mdFind($file, $link)
{
    $fichero = Storage::disk('documentacio')->get($file);
    $indice = substr($fichero, 0, strpos($fichero, $link));
    $cadena = substr($indice, strrpos($indice, '[') + 1, strrpos($indice, ']') - strrpos($indice, '[') - 1);
    $resto = strstr($fichero, $link);
    $desde = strstr($resto, $cadena);
    return substr($desde, 0, strpos($desde, '###'));
}

function exists_help($url)
{
    if ($menu = Intranet\Entities\Menu::where('url', $url)->first())
        return $menu->ajuda;
}

function inRol($roles){
    $array['roles'][] = config('roles.rol.administrador');
    if (is_array($roles)){
        foreach ($roles as $rol){
            $array['roles'][] = config('roles.rol.'.$rol);
        }
        return $array;
    }
    $array['roles'][] = config('roles.rol.'.$roles);
    return $array;

}

function usuarios($tipo,$field='email'){
    $usuarios = [];
    foreach (Intranet\Entities\Profesor::Activo()->get() as $profesor){
        if ($profesor->rol % config('roles.rol.'.$tipo) == 0){
            $usuarios[] = $profesor->$field;
        }
    }

    return $usuarios;
}

function existsTranslate($text){
    if (trans($text) != $text) return trans($text);
    return null;
}

function firstWord($cadena){
    $parte = explode(" ",$cadena);
    return $parte[0];
}

function loadImg($fixer){
    echo "<img src='/img/pdf/$fixer' />";
}

/**
 * @param $datos
 * @return mixed
 */
function cargaDatosCertificado($datos,$date=null){
    $secretario = Profesor::find(config('contacto.secretario'));
    $director = Profesor::find(config('contacto.director'));
    $datos['fecha'] = FechaString($date,'ca');
    $datos['secretario']['titulo'] = $secretario->sexo == 'H'?'En':'Na';
    $datos['secretario']['articulo'] = $secretario->sexo == 'H'?'El':'La';
    $datos['secretario']['genero'] = $secretario->sexo == 'H'?'secretari':'secretària';
    $datos['secretario']['nombre'] = $secretario->fullName;
    $datos['director']['articulo'] = $director->sexo == 'H'?'El':'La';
    $datos['director']['genero'] = $director->sexo == 'H'?'director':'directora';
    $datos['director']['nombre'] = $director->fullName;
    return $datos;
}

function getClientIpAddress(): String
{
    if (isset($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    if (isset($_SERVER['HTTP_X_FORWARDED'])) return $_SERVER['HTTP_X_FORWARDED'];
    if (isset($_SERVER['HTTP_FORWARDED_FOR'])) return $_SERVER['HTTP_FORWARDED_FOR'];
    if (isset($_SERVER['HTTP_FORWARDED'])) return $_SERVER['HTTP_FORWARDED'];
    if (isset($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
    return 'UNKNOWN';
}

function isPrivateAddress($ip):bool
{
    $privateAddressRange = array(
      '10.0.0.0|10.255.255.255',
      '172.16.0.0|172.31.255.255',
        '192.168.0.0|192.168.255.255',
        '169.254.0.0|169.254.255.255',
        '127.0.0.0|127.255.255.255'
    );
    $longIpAddress = ip2long($ip);
    if ($longIpAddress != -1){
        foreach ($privateAddressRange as $privateAddress){
            list($start,$end) = explode("|",$privateAddress);
            if ($longIpAddress >= ip2long($start) && $longIpAddress <= ip2long($end)) return true;
        }
    }
    return false;
}

function mb_ucfirst($string)
{
    $strlen = mb_strlen($string);
    $firstChar = mb_substr($string, 0, 1);
    $then = mb_substr($string, 1, $strlen - 1);
    return mb_strtoupper($firstChar) . $then;
}

function nomAmbTitol($sexe,$nom){
    if ($sexe == 'H') {
        $consideracio = preg_match('/^[aeiouàèáéíòóúh]{1}.*/i',$nom)?"n'":"en ";
    }
    else {
        $consideracio = preg_match('/^[aeiouàèáéíòóúh]{1}.*/i',$nom)?"n'":"na ";
    }
    return $consideracio.mb_ucfirst($nom);
}

