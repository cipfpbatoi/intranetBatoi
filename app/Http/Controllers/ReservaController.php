<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\IntranetController;

use Intranet\Entities\Espacio;
use Intranet\Entities\Hora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Reserva;

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
        $profesores = app(ProfesorService::class);
        if (esRol(AuthUser()->rol,config('roles.rol.direccion'))){
            $profes = $profesores->activosOrdered();
        } else
        {
            $profe = $profesores->find((string) AuthUser()->dni);
            $profes = collect(array_filter([$profe]));
        }

        return view('reservas.reserva', compact('espacios', 'horas', 'profes'));
    }


}
