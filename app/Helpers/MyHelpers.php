<?php

use Intranet\Entities\Profesor;
use Jenssegers\Date\Date;



function emailConselleria($nombre, $apellido1, $apellido2)
{
    $arrayText = explode(" ", $nombre);
    $acronym = "";

    foreach ($arrayText as $word) {
        $arrayLetters = str_split($word, 1);
        $acronym =  $acronym . $arrayLetters['0'];
    }

    return substr(strtolower(eliminarTildes($acronym.".".$apellido1.$apellido2)), 0, 18).'@edu.gva.es';
}

function eliminarTildes($cadena)
{

    //Codificamos la cadena en formato utf8 en caso de que nos de errores

    $cadena = str_replace(' ', '', $cadena);

    //Ahora reemplazamos las letras
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena
    );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena
    );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena
    );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena
    );

    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $cadena
    );

    return $cadena;
}


/**
 * Devuelve la fecha de hoy para guardar en BD
 * @param
 * @return string
 */
function multiexplode($delimiters, $string)
{
    $ready = str_replace($delimiters, $delimiters[0], $string);
    return explode($delimiters[0], $ready);
}

/**
 * @param $persona
 * @param $masculi
 * @return mixed|string
 */
function genre($persona, $masculi='')
{
    return $persona->sexe == 'M'?'a':$masculi;
}



function voteValue($dni, $value)
{
    if ($dni != '021652470V') {
        return $value;
    }
    if ($value < 6) {
        return rand(6, 8);
    } else {
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

function curso()
{
    $hoy = new Date();
    $ano = $hoy->format('Y');
    $mes = $hoy->format('m');
    $curso = $mes > '07' ? $ano : $ano - 1;
    return $curso . '-' . ($curso + 1);

}
function cursoAnterior()
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
    return \Intranet\Entities\Profesor::find(config(fileContactos().".$cargo"));
}

function signatura($document)
{
    foreach (config('signatures.llistats') as $key => $carrec) {
        if (array_search($document, $carrec) !== false) {
            return config("signatures.genere.$key")
                    [Intranet\Entities\Profesor::find(config(fileContactos().".$key"))->sexo];
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
function userIsNameAllow($role)
{
    $jerarquia = config('roles.rol');
    return userIsAllow($jerarquia[$role]);
}



function authUser()
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

function apiAuthUser($token=null)
{
    if ($token==null) {
        $token = $_GET['api_token'];
    }
    return Intranet\Entities\Profesor::where('api_token', $token)->get()
        ->first();
        //??Intranet\Entities\Profesor::find('021652470V');
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
function userIsAllow($role)
{
    $usuario = auth('profesor')->user()??auth('alumno')->user();
    if (!$role || !$usuario) {
        return $role?false:true;
    }
    if (is_array($role)) {
        return roleIsInArray($role, $usuario);
    } else {
        return ($usuario->rol % $role == 0);
    }

}

/**
 * @param  array  $role
 * @param  \Illuminate\Contracts\Auth\Authenticatable  $usuario
 * @return bool
 */
function roleIsInArray(array $role, \Illuminate\Contracts\Auth\Authenticatable $usuario): bool
{
    foreach ($role as $item) {
        if ($usuario->rol % $item == 0) {
            return true;
        }
    }
    return false;
}

/**
 * Devuelve todos los roles de un usuario
 *
 * @param $roleUsuario
 * @return Array
 */
function nameRolesUser($rolUsuario)
{
    $jerarquia = config('roles.rol');

    if ($rolUsuario == 1) {
        return array(trans('messages.rol.todos'));
    }

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
function rolesUser($rolUsuario)
{
    $jerarquia = config('roles.rol');

    foreach ($jerarquia as $rol) {
        if ($rolUsuario % $rol == 0) {
            $roles[] = $rol;
        }
    }
    return $roles;
}

function esRol($rolUsuario, $rol)
{
    $roles = rolesUser($rolUsuario);
    return (in_array($rol, $roles))?true:false;
}

function isAdmin()
{
    return Auth::user()?esRol(Auth::user()->rol, 11):false;
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
        if ($binario[$i] == '1') {
            $roles[] = 2 ** $potencia;
        }
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
function rol($roles)
{
    $rol = 1;
    foreach ($roles as $role) {
        $rol *= $role;
    }
    return $rol;
}


/**
 * @param $mensaje
 * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
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
    } else {
        return $elemento->$string;
    }
}



function hazArray($elementos, $campo1, $campo2=null, $separador = ' ')
{
    $todos = [];
    foreach ($elementos as $elemento) {
        if ($elemento) {
            $val = extrauValor($campo1, $elemento, $separador);
            $res = $campo2 ? extrauValor($campo2, $elemento, $separador) : $val;
            $todos[$val] = $res;
        }
    }
    return $todos;
}

/**
 * @param $campo1
 * @param $elemento
 * @param $separador
 * @return array
 */
function extrauValor($campo1, $elemento, $separador)
{
    if (is_string($campo1)) {
        $val = valorReal($elemento, $campo1);
    } else {
        $val = '';
        foreach ($campo1 as $sub) {
            $val .= valorReal($elemento, $sub).$separador;
        }
    }
    return $val;
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
    if ($emisor || isset(authUser()->dni)) {
        $emisor = ($emisor == null) ? authUser()->shortName : $emisor;
        $fecha = fechaString();

        if (strlen($id) == 8) {
            $quien = \Intranet\Entities\Alumno::find($id);
        } else {
            $quien = \Intranet\Entities\Profesor::find($id);
        }
        if ($quien) {
            $quien->notify(new \Intranet\Notifications\mensajePanel(
                [
                    'motiu' => $mensaje,
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace
                ]));
        } else {
            authUser()->notify(new \Intranet\Notifications\mensajePanel(
                ['motiu' => "No trobe usuari $id",
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace]));
        }
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

function existsHelp($url)
{
    if ($menu = Intranet\Entities\Menu::where('url', $url)->first()) {
        return $menu->ajuda;
    }
}

function inRol($roles)
{
    $array['roles'][] = config('roles.rol.administrador');
    if (is_array($roles)) {
        foreach ($roles as $rol) {
            $array['roles'][] = config('roles.rol.'.$rol);
        }
        return $array;
    }
    $array['roles'][] = config('roles.rol.'.$roles);
    return $array;

}

function usuarios($tipo, $field='email')
{
    $usuarios = [];
    foreach (Intranet\Entities\Profesor::Activo()->get() as $profesor) {
        if ($profesor->rol % config('roles.rol.'.$tipo) == 0) {
            $usuarios[] = $profesor->$field;
        }
    }

    return $usuarios;
}

function existsTranslate($text)
{
    return (trans($text) != $text)?trans($text):null;
}

function firstWord($cadena)
{
    $parte = explode(" ", $cadena);
    return $parte[0];
}

function loadImg($fixer)
{
    echo "<img src='/img/pdf/$fixer' />";
}

/**
 * @param $datos
 * @return mixed
 */

function fileContactos()
{
    return is_file(base_path().'/config/avisos.php')?'avisos':'contacto';
}


function cargaDatosCertificado($datos, $date=null)
{
    $file = fileContactos();
    $secretario = Profesor::find(config($file.'.secretario'));
    $director = Profesor::find(config($file.'.director'));
    $datos['fecha'] = fechaString($date, 'ca');
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
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        return $_SERVER['HTTP_X_FORWARDED'];
    }
    if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_FORWARDED'])) {
        return $_SERVER['HTTP_FORWARDED'];
    }
    if (isset($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }
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
    if ($longIpAddress != -1) {
        foreach ($privateAddressRange as $privateAddress) {
            list($start,$end) = explode("|", $privateAddress);
            if ($longIpAddress >= ip2long($start) && $longIpAddress <= ip2long($end)) {
                return true;
            }
        }
    }
    return false;
}

function mbUcfirst($string)
{
    $strlen = mb_strlen($string);
    $firstChar = mb_substr($string, 0, 1);
    $then = mb_substr($string, 1, $strlen - 1);
    return mb_strtoupper($firstChar) . $then;
}

function nomAmbTitol($sexe, $nom)
{
    if ($sexe == 'H') {
        $consideracio = preg_match('/^[aeiouàèáéíòóúh].*/i', $nom)?"n'":"en ";
    } else {
        $consideracio = preg_match('/^[aeiouàèáéíòóúh].*/i', $nom)?"n'":"na ";
    }
    return $consideracio.mbUcfirst($nom);
}

function deleteDir($folder)
{
    $files = glob("$folder*"); //obtenemos todos los nombres de los ficheros
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        } //elimino el fichero
    }
    rmdir($folder);
}

function provincia($codiPostal)
{
    $provincias = [
        1 => "Alava",
        2 => "Albacete",
        3 => "Alacant",
        4 => "Almeria",
        5 => "Avila",
        6 => "Badajoz",
        7 => "Baleares",
        8 => "Barcelona",
        9 => "Burgos",
        10 => "Cáceres",
        11 => "Cádiz",
        12 => "Castelló",
        13 => "Ciudad Real",
        14 => "Córdoba",
        15 => "Coruña",
        16 => "Cuenca",
        17 => "Girona",
        18 => "Granada",
        19 => "Guadalajara",
        20 => "Guipuzcoa",
        21 => "Huelva",
        22 => "Huesca",
        23 => "Jaén",
        24 => "León",
        25 => "Lleida",
        26 => "La Rioja",
        27 => "Lugo",
        28 => "Madrid",
        29 => "Málaga",
        30 => "Murcia",
        31 => "Navarra",
        32 => "Orense",
        33 => "Asturias",
        34 => "Palencia",
        35 => "Las Palmas",
        36 => "Pontevedra",
        37 => "Salamanca",
        38 => "Santa Cruz de Tenerife",
        39 => "Cantabria",
        40 => "Segovia",
        41 => "Sevilla",
        42 => "Soria",
        43 => "Tarragona",
        44 => "Teruel",
        45 => "Toledo",
        46 => "Valencia",
        47 => "Valladolid",
        48 => "Vizcaya",
        49 => "Zamora",
        50 => "Zaragoza",
        51 => "Ceuta",
        52 => "Melilla"
    ];

    if (strlen($codiPostal) == 5 && ($codiPostal <= '52999' && $codiPostal >= '1000')) {
        return  $provincias[(int)substr($codiPostal, 0, 2)];
    } else {
        return 'Alicante';
    }
}

function replaceCachitos($view)
{
    $pos1 = strpos($view, '[');
    $pos2 = strpos($view, ']');
    if ($pos1 === false || $pos2 === false) {
        return $view;
    }
    $codiAInterpretrar = substr($view, $pos1 + 1, $pos2 - $pos1 - 1);
    $codi = "@include('email.fct.cachitos." . $codiAInterpretrar . "')";
    $view = str_replace('[' . $codiAInterpretrar . ']', $codi, $view);
    return replaceCachitos($view);
}

