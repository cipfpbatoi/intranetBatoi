<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Espacio;
use Intranet\Entities\Hora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Profesor;

/**
 * Class ReservaController
 * @package Intranet\Http\Controllers
 */
class ReservaController extends IntranetController
{
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Reserva';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        Session::forget('redirect');
        $espacios = Espacio::where('reservable',1)->get();
        $horas = Hora::all();
        if (esRol(AuthUser()->rol,config('roles.rol.direccion'))){
            $profes = Profesor::Activo()->orderBy('apellido1')->get();
        } else
        {
            $profes = Profesor::where('dni',AuthUser()->dni)->get();
        }

        return view('reservas.reserva', compact('espacios', 'horas','profes'));
    }
}
