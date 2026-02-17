<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;

use Intranet\Services\HR\FitxatgeService;
use Styde\Html\Facades\Alert;


class FicharController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Falta_profesor';
    protected $gridFields = ['Xdepartamento', 'FullName', 'Horario', 'Entrada', 'Salida'];
    protected $parametresVista = ['before' => ['formulario']];
    protected $amount= 200;

    private ?ProfesorService $profesorService = null;
    private ?HorarioService $horarioService = null;

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
    }

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
        return $this->profesores()->activos();
    }



    public function store(Request $request )
    {
        $fitxatgeService = app( FitxatgeService::class);

        $profesor = $this->profesores()->findByCodigo((string) $request->codigo);

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
        $profes = $this->profesores()->plantillaOrderedByDepartamento();
        return view('fichar.control', compact('profes'));
    }


    public function controlDia()
    {
        $horarios = $this->loadHoraries(
            $profes=$this->profesores()->plantillaOrderedByDepartamento());
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
        $horario = $this->horarios()->primeraByProfesorAndDateOrdered((string) $profesor->dni, FechaInglesa(Hoy()));

        if (isset($horario->first()->desde)) {
            return $horario->first()->desde . " - " . $horario->last()->hasta;
        }
        return '';
    }

    public function resumenRango()
    {
        // Professors en plantilla, amb nom complet per al combo
        $profes = $this->profesores()->plantillaForResumen();

        return view('fichar.resumen-rango', compact('profes'));
    }

}
