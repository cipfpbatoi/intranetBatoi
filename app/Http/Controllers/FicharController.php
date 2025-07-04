<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;

use \DB;
use Intranet\Entities\Profesor;
use Intranet\Entities\Horario;
use Intranet\Services\FitxatgeService;
use Styde\Html\Facades\Alert;


class FicharController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Falta_profesor';
    protected $gridFields = ['Xdepartamento', 'FullName', 'Horario', 'Entrada', 'Salida'];
    protected $parametresVista = ['before' => ['formulario']];
    protected $amount= 200;

    public function ficha(FitxatgeService $fitxatgeService)
    {
        $fitxatgeService->fitxar(); // usa l’usuari autenticat per defecte

        if (!estaDentro()) {
            return redirect('/logout');
        }

        return back();
    }
    
    public function search()
    {
        return Profesor::activo()->get();
    }



    public function store(Request $request )
    {
        $fitxatgeService = app( FitxatgeService::class);

        $profesor = Profesor::select('dni', 'nombre', 'apellido1', 'apellido2')
            ->where('codigo', '=', $request->codigo)
            ->first();

        if (!$profesor) {
            Alert::danger(trans('messages.generic.nocodigo'));
            return back();
        }

        $fichaje = $fitxatgeService->fitxar($profesor->dni);

        if ($fichaje === null) {
            Alert::danger(trans('messages.generic.acaba')); // Ja ha fitxat fa menys de 10 min
            return back();
        }

        if ($fichaje === false) {
            Alert::danger(trans('messages.generic.fueraCentro')); // IP no vàlida
            return back();
        }

        if ($fichaje->salida !== null) {
            Alert::info(trans('messages.generic.sale') . ' ' . $profesor->FullName . ' a ' . $fichaje->salida);
        } else {
            Alert::success(trans('messages.generic.entra') . ' ' . $profesor->FullName . ' a ' . $fichaje->entrada);
        }

        return back();
    }

    public function control()
    {
        $profes = Profesor::select('dni', 'nombre', 'apellido1', 'apellido2', 'departamento')->orderBy('departamento')
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->Plantilla()
            ->get();
        return view('fichar.control', compact('profes'));
    }

    public function controlDia()
    {
        $horarios = $this->loadHoraries(
            $profes=Profesor::Plantilla()->orderBy('departamento')->orderBy('apellido1')->orderBy('apellido2')->get());
        return view('fichar.control-dia', compact('profes', 'horarios'));
    }
    private function loadHoraries($profesores){
        $horarios = array();
        foreach ($profesores as $profesor) {
            $profesor->departamento = $profesor->Departamento ?  $profesor->Departamento->depcurt : '';
            $horarios[$profesor->dni] = $this->loadHorary($profesor);

        }
        return $horarios;
    }
    private function loadHorary($profesor) {
        $horario = Horario::Primera($profesor->dni, FechaInglesa(Hoy()))->orderBy('sesion_orden')->get();

        if (isset($horario->first()->desde)) {
            return $horario->first()->desde . " - " . $horario->last()->hasta;
        }
        return '';
    }

}
