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
    protected $vista = ['index' => 'fichar.index'];
    protected $gridFields = ['departamento', 'nombre', 'horario', 'entrada', 'salida'];

    public function ficha()
    {
        Falta_profesor::fichar();
        if (!estaDentro()) {
            //Alert::info(trans('messages.generic.sale').' '. AuthUser()->nombre);
            return redirect('/logout');
        } else
            return back();
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
        }
        else {
            Alert::danger(trans('messages.generic.nocodigo'));
        }
        return back();
    }

    public function control()
    {
        $profes = Profesor::select('dni', 'nombre', 'apellido1', 'apellido2', 'departamento')->orderBy('departamento')->orderBy('apellido1')->orderBy('apellido2')->Plantilla()->get();
        return view('fichar.control', compact('profes'));
    }

    public function controlDia()
    {
        $profes = Profesor::select('dni', 'nombre', 'apellido1', 'apellido2', 'departamento', 'email')->orderBy('departamento')->orderBy('apellido1')->orderBy('apellido2')->Plantilla()->get();
        $fecha=date("Y-m-d");
        $horarios=Array();
        foreach ($profes as $profesor) {
            // Obtenemso el nomcurt del departamento
            $profesor->departamento = $profesor->Departamento->depcurt;
            // Obtenemos su horario
            $horario = Horario::Primera($profesor->dni,$fecha)->orderBy('sesion_orden')->get();
            if (isset($horario->first()->desde)) {
                $profesor->email = $horario->first()->desde . " - " . $horario->last()->hasta;
                $horarios[$profesor->dni] = $horario->first()->desde . " - " . $horario->last()->hasta;
            } else {
                $profesor->email = '';
                $horarios[$profesor->dni] = '';
            }
        }
        return view('fichar.control-dia', compact('profes', 'horarios'));
    }

}
