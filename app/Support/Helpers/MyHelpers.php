<?php

use Intranet\Application\Profesor\ProfesorService;
use Jenssegers\Date\Date;

/**
 * Resol el servei de professorat per als helpers globals.
 *
 * @return ProfesorService
 */
function profesorService(): ProfesorService
{
    return app(ProfesorService::class);
}

/**
 * Genera una URL d'asset amb versió basada en `filemtime` per evitar caché antic.
 *
 * @param string $path
 * @return string
 */
function asset_nocache(string $path)
{
    $realPath = public_path($path);

    $version = file_exists($realPath)
        ? filemtime($realPath)
        : time();

    return asset($path) . '?v=' . $version;
}

/**
 * Genera un correu institucional de Conselleria a partir del nom i cognoms.
 *
 * @param string $nombre
 * @param string $apellido1
 * @param string $apellido2
 * @return string
 */
function emailConselleria($nombre, $apellido1, $apellido2)
{
    $arrayText = explode(" ", $nombre);
    $acronym = "";

    foreach ($arrayText as $word) {
        $acronym .= mb_substr($word, 0, 1);
    }

    return substr(strtolower(eliminarTildes($acronym.".".$apellido1.$apellido2)), 0, 18).'@edu.gva.es';
}

/**
 * Elimina espais i accents d'una cadena.
 *
 * @param string $cadena
 * @return string
 */
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
 * @param $persona
 * @param $masculi
 * @return mixed|string
 */
function genre($persona, $masculi='')
{
    return $persona->sexe == 'M'?'a':$masculi;
}


/**
 * Ajusta aleatòriament el valor d'una votació per a un DNI concret.
 *
 * @param string $dni
 * @param int|float $value
 * @return int|float
 */
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

/**
 * Retorna l'avaluació actual segons les dates configurades en `curso.evaluaciones`.
 *
 * @return int
 */
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

/**
 * Retorna el curs acadèmic actual (`YYYY-YYYY+1`).
 *
 * @return string
 */
function curso()
{
    $hoy = new Date();
    $ano = $hoy->format('Y');
    $mes = $hoy->format('m');
    $curso = $mes > '07' ? $ano : $ano - 1;
    return $curso . '-' . ($curso + 1);

}

/**
 * Retorna el curs acadèmic anterior.
 *
 * @return string
 */
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

/**
 * Retorna el professor associat a un càrrec configurat.
 *
 * @param string $cargo
 * @return \Intranet\Entities\Profesor|null
 */
function cargo($cargo)
{
    return profesorService()->find((string) config("avisos.$cargo"));
}

/**
 * Retorna la forma textual de signatura adequada al document i gènere de qui signa.
 *
 * @param string $document
 * @return string|null
 */
function signatura($document)
{
    foreach (config('signatures.llistats') as $key => $carrec) {
        if (array_search($document, $carrec) !== false) {
            $profesor = profesorService()->find((string) config("avisos.$key"));
            if (!$profesor) {
                return null;
            }

            return config("signatures.genere.$key")[$profesor->sexo];
        }
    }
}

/**
 * Retorna el codi d'imatge de signatura per a un document.
 *
 * @param string $document
 * @return string|null
 */
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
/**
 * Retorna l'usuari autenticat de `profesor` o, en defecte, d'`alumno`.
 *
 * @return \Illuminate\Contracts\Auth\Authenticatable|null
 */
function authUser(): \Illuminate\Contracts\Auth\Authenticatable | null
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

/**
 * Resol l'usuari professor autenticat per API token.
 *
 * @param string|null $token
 * @return \Intranet\Entities\Profesor|null
 */
function apiAuthUser($token=null)
{
    if ($token==null) {
        $token = $_GET['api_token']??null;
    }
    return $token ? profesorService()->findByApiToken((string) $token) : null;
}

/**
 * Comprova si l'usuari autenticat és professor.
 *
 * @return bool
 */
function isProfesor()
{
    return auth('profesor')->check();
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
    $roles = [];
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

/**
 * Comprova si un rol concret està inclòs dins del rol compost de l'usuari.
 *
 * @param int $rolUsuario
 * @param int $rol
 * @return bool
 */
function esRol($rolUsuario, $rol)
{
    $roles = rolesUser($rolUsuario);
    return in_array($rol, $roles);
}

/**
 * Comprova si l'usuari autenticat té rol d'administració (11).
 *
 * @return bool
 */
function isAdmin()
{
    return Auth::user()?esRol(Auth::user()->rol, 11):false;
}

/**
 * Retorna els DNI dels professors actius que compleixen un rol determinat.
 *
 * @param int $rol
 * @return array
 */
function usersWithRol($rol)
{
    $usuarios = [];
    foreach (profesorService()->activos() as $usuario) {
        if (esRol($usuario->rol, $rol)) {
            $usuarios[] = $usuario->dni;
        }
    }
    return $usuarios;
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

/**
 * Indica si una clau de traducció no existeix.
 *
 * @param string $mensaje
 * @return bool
 */
function isblankTrans($mensaje)
{
    return trans($mensaje) == $mensaje;
}

/**
 * Resol una propietat simple o anidada (`foo->bar`) d'un element.
 *
 * @param mixed $elemento
 * @param string $string
 * @return mixed
 */
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
/**
 * Construeix un array associatiu a partir d'una col·lecció/lista d'elements.
 *
 * @param iterable $elementos
 * @param string|array $campo1
 * @param string|array|null $campo2
 * @param string $separador
 * @return array
 */
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

/**
 * Retorna el nom curt d'una classe o entitat.
 *
 * @param object $elemento
 * @return string
 */
function getClase($elemento)
{
    $clase = get_class($elemento);

    // Si la classe comença amb "Intranet\Entities\", eliminem el prefix
    if (str_starts_with($clase, "Intranet\Entities\\")) {
        return substr($clase, strlen("Intranet\Entities\\"));
    }

    // Si no, simplement retornem el nom curt de la classe
    return (new \ReflectionClass($elemento))->getShortName();
}

/**
 * Retorna el nom curt d'una FQCN d'entitat.
 *
 * @param string $str
 * @return string
 */
function getClass($str)
{
    return substr($str, strlen("Intranet\Entities\\"));
}

/**
 * Envia notificació interna a alumne/professor.
 *
 * @param string $id
 * @param string $mensaje
 * @param string $enlace
 * @param string|null $emisor
 * @return void
 */
function avisa($id, $mensaje, $enlace = '#', $emisor = null)
{
    if ($emisor || isset(authUser()->dni)) {
        $emisor = ($emisor == null) ? authUser()->shortName : $emisor;
        $fecha = fechaString();

        if (strlen($id) == 8) {
            $quien = \Intranet\Entities\Alumno::find($id);
        } else {
            $quien = profesorService()->find((string) $id);
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

/**
 * Retorna el valor de la clau primària de l'element.
 *
 * @param object $elemento
 * @return mixed
 */
function primryKey($elemento)
{
    $primaryKey = isset($elemento->primaryKey) ? $elemento->primaryKey : 'id';
    return $elemento->$primaryKey;
}

/**
 * Substitueix valors en un Request i retorna una còpia.
 *
 * @param Illuminate\Http\Request $request
 * @param array $fields
 * @return Illuminate\Http\Request
 */
function subsRequest(Illuminate\Http\Request $request, $fields)
{
    foreach ($fields as $key => $value) {
        $dades = $request->except($key);
        $dades[$key] = $value;
        $request = $request->duplicate(null, $dades);
    }
    return $request;
}

/**
 * Retalla un fragment de documentació markdown des d'un enllaç concret.
 *
 * @param string $file
 * @param string $link
 * @return string
 */
function mdFind($file, $link)
{
    $fichero = Storage::disk('documentacio')->get($file);
    $indice = substr($fichero, 0, strpos($fichero, $link));
    $cadena = substr($indice, strrpos($indice, '[') + 1, strrpos($indice, ']') - strrpos($indice, '[') - 1);
    $resto = strstr($fichero, $link);
    $desde = strstr($resto, $cadena);
    return substr($desde, 0, strpos($desde, '###'));
}

/**
 * Retorna si hi ha ajuda associada a una URL de menú.
 *
 * @param string $url
 * @return mixed
 */
function existsHelp($url)
{
    if ($menu = Intranet\Entities\Menu::where('url', $url)->first()) {
        return $menu->ajuda;
    }
}

/**
 * Prepara una estructura `['roles' => [...]]` per passar-la a components/polítiques de UI.
 *
 * @param string|array $roles
 * @return array
 */
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

/**
 * Retorna la traducció o `null` si no existeix.
 *
 * @param string $text
 * @return string|null
 */
function existsTranslate($text)
{
    $translated = trans($text);
    return $translated != $text ? $translated : null;
}

/**
 * Retorna la primera paraula d'una cadena separada per espais.
 *
 * @param string $cadena
 * @return string
 */
function firstWord($cadena)
{
    $parte = explode(" ", $cadena);
    return $parte[0];
}

/**
 * @param $datos
 * @return mixed
 */

function cargaDatosCertificado($datos, $date=null)
{
    $secretario = profesorService()->find((string) config('avisos.secretario'));
    $director = profesorService()->find((string) config('avisos.director'));
    if (!$secretario || !$director) {
        return $datos;
    }
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

/**
 * Obté l'adreça IP client des de capçaleres comunes o `REMOTE_ADDR`.
 *
 * @return string
 */
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

/**
 * Comprova si una IP pertany a rangs privats/predefinits de confiança.
 *
 * @param string $ip
 * @return bool
 */
function isPrivateAddress($ip):bool
{
    $privateAddressRange = array(
      '213.0.87.0|213.0.87.255',
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

/**
 * Capitalitza el primer caràcter d'una cadena multibyte.
 *
 * @param string $string
 * @return string
 */
function mbUcfirst($string)
{
    $strlen = mb_strlen($string);
    $firstChar = mb_substr($string, 0, 1);
    $then = mb_substr($string, 1, $strlen - 1);
    return mb_strtoupper($firstChar) . $then;
}

/**
 * Afig tractament (`en`, `na`, `n'`) a un nom segons sexe i vocal inicial.
 *
 * @param string $sexe
 * @param string $nom
 * @return string
 */
function nomAmbTitol($sexe, $nom)
{
    if ($sexe == 'H') {
        $consideracio = preg_match('/^[aeiouàèáéíòóúh].*/i', $nom)?"n'":"en ";
    } else {
        $consideracio = preg_match('/^[aeiouàèáéíòóúh].*/i', $nom)?"n'":"na ";
    }
    return $consideracio.mbUcfirst($nom);
}

/**
 * Elimina tots els fitxers d'una carpeta i, després, la carpeta.
 *
 * @param string $folder
 * @return void
 */
function deleteDir($folder)
{
    $folder = rtrim($folder, '/').'/';
    $files = glob($folder . '*'); //obtenemos todos los nombres de los ficheros
    if ($files === false) {
        return;
    }
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        } //elimino el fichero
    }
    if (is_dir($folder)) {
        rmdir($folder);
    }
}

/**
 * Retorna el nom de província a partir del codi postal espanyol.
 *
 * @param string $codiPostal
 * @return string
 */
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
/**
 * Substitueix tokens `[nom]` per `@include('email.fct.cachitos.nom')` de manera recursiva.
 *
 * @param string $view
 * @return string
 */
function replaceCachitos($view)
{
    $pos1 = strpos($view, '[');
    $pos2 = strpos($view, ']');

    if ($pos1 === false || $pos2 === false || $pos2 <= $pos1) {
        return $view;
    }

    $codiAInterpretrar = substr($view, $pos1 + 1, $pos2 - $pos1 - 1);

    // Només acceptem tokens "nets" → lletres, números, _ i -
    // Han de començar per lletra (majúscula o minúscula)
    if (preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $codiAInterpretrar)) {
        $codi = "@include('email.fct.cachitos." . $codiAInterpretrar . "')";
        $view = str_replace('[' . $codiAInterpretrar . ']', $codi, $view);
    } else {
        return $view;
    }

    // Si no és vàlid (p. ex. [0.75em]), el deixem literal
    return replaceCachitos($view);
}

/**
 * Retalla valors llargs de forma segura, normalitzant tipus comuns (array, bool, dates...).
 *
 * @param mixed $item
 * @param int $long
 * @return string
 */
function in_substr($item, int $long)
{
    // Converteix collections a array
    if ($item instanceof Illuminate\Support\Collection) {
        $item = $item->all();
    }

    // Arrays → fem log i convertim
    if (is_array($item)) {
        try {
            Illuminate\Support\Facades\Log::warning('[in_substr] Rebut array en compte de string', [
                'url'    => request()->fullUrl() ?? null,
                'route'  => optional(request()->route())->getName(),
                'count'  => count($item),
                'sample' => array_slice(array_map(
                    fn($v) => is_scalar($v) ? (string)$v : gettype($v),
                    $item
                ), 0, 5),
                // útil per a saber quina vista
                'trace'  => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8))
                                ->pluck('file')
                                ->filter()
                                ->implode(' | '),
            ]);
        } catch (\Throwable $e) {
            // si falla el log, no trenquem res
        }

        $item = implode(', ', array_map('strval', $item));
    }

    // Dates
    if ($item instanceof Carbon\CarbonInterface) {
        $item = $item->toDateTimeString();
    }

    // Bools
    if (is_bool($item)) {
        $item = $item ? 'Sí' : 'No';
    }

    // Nulls
    if ($item === null) {
        $item = '';
    }

    // Altres tipus
    if (!is_string($item)) {
        $item = (string) $item;
    }

    // Retall multibyte
    if (mb_strlen($item) <= $long) {
        return $item;
    }

    return mb_substr($item, 0, $long) . '…';
}
/**
 * Calcula la profunditat màxima d'un array multidimensional.
 *
 * @param mixed $array
 * @return int
 */
function array_depth($array) {
    if (!is_array($array)) {
        return 0;
    }

    $max_depth = 1;

    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = array_depth($value) + 1;

            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }

    return $max_depth;
}

/**
 * Retorna la clau associada a un tipus FCT en configuració.
 *
 * @param string $tipus
 * @return int|string|null
 */
function asociacion_fct($tipus)
{
    // Accedir a la configuració de l'array 'tipusFCT'
    $tipusFCT = config('auxiliares.tipusFCT');

    // Buscar la clau associada al valor donat
    $clau = array_search($tipus, $tipusFCT);

    // Retornar la clau si s'ha trobat, o null si no s'ha trobat
    return $clau !== false ? $clau : null;
}
