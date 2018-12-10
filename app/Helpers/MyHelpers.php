<?php

/**
 * Devuelve la fecha de hoy para guardar en BD
 *
 * @param 
 * @return string
 */
function multiexplode($delimiters, $string)
{
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return $launch;
}

function Entrada()
{
    if (isset(Intranet\Entities\Falta_profesor::Hoy(AuthUser()->dni)->last()->entrada))
        return (substr(Intranet\Entities\Falta_profesor::Hoy(AuthUser()->dni)->last()->entrada, 0, 5));
}

function Salida()
{
    if (isset(Intranet\Entities\Falta_profesor::Hoy(AuthUser()->dni)->last()->salida))
        return (substr(Intranet\Entities\Falta_profesor::Hoy(AuthUser()->dni)->last()->salida, 0, 5));
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
    $lcurso = $curso . '-' . ($curso + 1);
    return $lcurso;
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

function signatura($document)
{
    foreach (config('signatures.llistats') as $key => $carrec) {
        if (array_search($document, $carrec) !== false) {
            return config("signatures.genere.$key")
                    [Intranet\Entities\Profesor::find(config("contacto.$key"))->sexo];
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
        if (auth('alumno')->user())
            $usuario = auth('alumno')->user();
    }
    return $usuario;
}

function isProfesor()
{
    if (auth('profesor')->user())
        return true;
    else
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
    if ($role == null)
        return true;
    if (auth('profesor')->user()) {
        $usuario = auth()->user();
    } else {
        $usuario = auth('alumno')->user();
    }
    if ($usuario == null)
        return false;
    if (is_array($role)) {
        foreach ($role as $item) {
            if ($usuario->rol % $item == 0)
                return true;
        }
    }
    else if ($usuario->rol % $role == 0)
        return true;
    return false;
}

/**
 * Devuelve todos los roles de un usuario
 *
 * @param usuario $role
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
