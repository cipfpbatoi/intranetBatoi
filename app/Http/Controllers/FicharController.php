<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Actividad;
use Intranet\Entities\Falta;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Http\Requests\FicharStoreRequest;

use Intranet\Services\HR\FitxatgeService;
use Intranet\Services\UI\AppAlert as Alert;


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

        if (!$fitxatgeService->isInside(null, true)) {
            return redirect()->route('logout');
        }

        return back();
    }
    
    public function search()
    {
        $incidencies = $this->dnisAmbIncidenciaDia(Hoy());

        return $this->profesores()->activos()->map(function ($profesor) use ($incidencies) {
            if (in_array((string) $profesor->dni, $incidencies, true)) {
                $profesor->FullName = '<span class="text-danger">' . e((string) $profesor->FullName) . '</span>';
            }

            return $profesor;
        });
    }

    /**
     * Retorna els DNIs amb absència o activitat extraescolar en un dia.
     *
     * @param string $dia
     * @return array<int, string>
     */
    private function dnisAmbIncidenciaDia(string $dia): array
    {
        $dnisFalta = Falta::query()
            ->Dia($dia)
            ->pluck('idProfesor')
            ->map(static fn ($dni): string => (string) $dni)
            ->all();

        $dnisActivitat = [];
        $activitats = Actividad::query()
            ->Dia($dia)
            ->where('fueraCentro', '=', 1)
            ->with('profesores:dni')
            ->get();

        foreach ($activitats as $activitat) {
            foreach ($activitat->profesores as $profesor) {
                $dnisActivitat[] = (string) $profesor->dni;
            }
        }

        return array_values(array_unique(array_merge($dnisFalta, $dnisActivitat)));
    }



    public function store(Request $request )
    {
        $this->validate($request, (new FicharStoreRequest())->rules());
        $fitxatgeService = app( FitxatgeService::class);

        $profesor = $this->profesores()->findByCodigo((string) $request->codigo);

        if (!$profesor) {
            Alert::danger(__('messages.generic.nocodigo'));
            return back();
        }

        $fichaje = $fitxatgeService->fitxar($profesor->dni);

        if ($fichaje === null) {
            Alert::danger(__('messages.generic.acaba')); // Ja ha fitxat fa menys de 10 min
            return back();
        }

        if ($fichaje === false) {
            Alert::danger(__('messages.generic.fueraCentro')); // IP no vàlida
            return back();
        }

        if ($fichaje->salida !== null) {
            Alert::info(__('messages.generic.sale') . ' ' . $profesor->FullName . ' a ' . $fichaje->salida);
        } else {
            Alert::success(__('messages.generic.entra') . ' ' . $profesor->FullName . ' a ' . $fichaje->entrada);
        }

        return back();
    }

    public function control()
    {
        $profes = $this->profesores()
            ->plantillaOrderedByDepartamento()
            ->map(static function ($profesor): array {
                return [
                    'dni' => $profesor->dni,
                    'nombre' => $profesor->nombre,
                    'apellido1' => $profesor->apellido1,
                    'apellido2' => $profesor->apellido2,
                    'depcurt' => $profesor->Xdepartamento,
                ];
            })
            ->values();

        return view('fichar.control', compact('profes'));
    }


    public function controlDia()
    {
        return view('fichar.control-dia');
    }

    private function loadHoraries($profesores){
        $horarios = array();
        foreach ($profesores as $profesor) {
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
