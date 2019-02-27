<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Falta_profesor;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use \DB;
use Illuminate\Support\Facades\Redirect;
use Intranet\Entities\Profesor;
use Intranet\Entities\Actividad;
use Intranet\Entities\Actividad_profesor;
use Intranet\Entities\Comision;
use Intranet\Entities\Falta;
use Intranet\Entities\Horario;
use Intranet\Botones\Panel;
use Styde\Html\Facades\Alert;
use Styde\Html;
use Intranet\Botones\BotonImg;

class FicharController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Falta_profesor';
    protected $gridFields = ['Xdepartamento', 'FullName', 'Horario', 'Entrada', 'Salida'];
    protected $parametresVista = ['before' => ['formulario']];
    protected $amount= 200;
    
    public function ficha()
    {
        Falta_profesor::fichar();
        if (!estaDentro())  return redirect('/logout');
        return back();
    }
    
    public function search()
    {
        return Profesor::activo()->get();
    }


    public function store(Request $request)
    {
        $profesor = Profesor::select('dni', 'nombre', 'apellido1', 'apellido2')->where('codigo', '=', $request->codigo)->first();
        if (isset($profesor->dni)) {
            $fichaje = Falta_profesor::fichar($profesor->dni);
            if ($fichaje->salida != null)
                Alert::info(trans('messages.generic.sale') . ' ' . $profesor->FullName . ' a ' . $fichaje->salida);
            else
                Alert::success(trans('messages.generic.entra') . ' ' . $profesor->FullName . ' a ' . $fichaje->entrada);
            return back();
        }

        Alert::danger(trans('messages.generic.nocodigo'));
        return back();
    }

    public function control()
    {
        $profes = Profesor::select('dni', 'nombre', 'apellido1', 'apellido2', 'departamento')->orderBy('departamento')->orderBy('apellido1')->orderBy('apellido2')->Plantilla()->get();
        return view('fichar.control', compact('profes'));
    }

    public function controlDia()
    {
        $horarios = $this->loadHoraries($profes=Profesor::Plantilla()->orderBy('departamento')->orderBy('apellido1')->orderBy('apellido2')->get());
        return view('fichar.control-dia', compact('profes', 'horarios'));
    }
    private function loadHoraries($profesores){
        $horarios = Array();
        foreach ($profesores as $profesor) {
            $profesor->departamento = $profesor->Departamento->depcurt;
            $horarios[$profesor->dni] = $this->loadHorary($profesor);

        }
        return $horarios;
    }
    private function loadHorary($profesor){
        $horario = Horario::Primera($profesor->dni,FechaInglesa(Hoy()))->orderBy('sesion_orden')->get();

        if (isset($horario->first()->desde))
             return $horario->first()->desde . " - " . $horario->last()->hasta;

        return '';

    }


}
