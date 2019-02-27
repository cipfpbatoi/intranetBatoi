<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Espacio;
use Intranet\Entities\Hora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        return view('reservas.reserva', compact('espacios', 'horas'));
    }
}
