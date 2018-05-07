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
    protected $vista = ['list' => 'llist.ausencia', 'index' => 'fichar.index'];
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

    public function listado($dia = null)
    {
        $dia = $dia ? $dia : Hoy();
        $fdia = new Date($dia);
        $todos = Profesor::whereIn('dni', self::noHanFichado($dia))->get();
        foreach ($todos as $profesor) {
            $profesor->departamento = $profesor->Departamento->literal;
        }
        $panel = new Panel('Profesor', ['departamento', 'apellido1', 'apellido2', 'nombre', 'email'], 'grid.standard');
        $panel->dia = $fdia->toDateString();
        $panel->anterior = $fdia->subDay()->toDateString();
        $panel->posterior = $fdia->addDays(2)->toDateString();
        $panel->setBoton('grid', new BotonImg('fichar.delete', [], 'direccion', $panel->dia));
        
        return $this->llist($todos, $panel);
    }

    public function deleteDia($usuario, $dia)
    {
        Falta_profesor::fichaDia($usuario, $dia);
        return back();
    }

    public static function noHanFichado($dia)
    {
        $profesores = Profesor::select('dni')->Activo()->get();
        
        // mira qui no ha fitxat
        $noHanFichado = [];
        foreach ($profesores as $profesor) {
            if (Falta_profesor::haFichado($dia, $profesor->dni)->count() == 0)
                if (Horario::Profesor($profesor->dni)->Dia(nameDay(new Date($dia)))->count() > 1)
                    $noHanFichado[$profesor->dni] = $profesor->dni;
        }


        // comprova que no estigues d'activitat
        $actividades = Actividad::Dia($dia)->where('fueraCentro','=',1)->get();
        foreach ($actividades as $actividad) {
            foreach ($actividad->profesores as $profesor) {
                if (in_array($profesor->dni, $noHanFichado))
                    unset($noHanFichado[$profesor->dni]);
            }
        }

        // comprova que no està de comissió
        $comisiones = Comision::Dia($dia)->get();
        foreach ($comisiones as $comision) {
            if (in_array($comision->idProfesor, $noHanFichado))
                unset($noHanFichado[$comision->idProfesor]);
        }

        // compova que no tinga falta
        $faltas = Falta::Dia($dia)->get();
        foreach ($faltas as $falta) {
            if (in_array($falta->idProfesor, $noHanFichado))
                unset($noHanFichado[$falta->idProfesor]);
        }
        
        return $noHanFichado;
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
