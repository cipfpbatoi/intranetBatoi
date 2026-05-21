<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Actividad;
use Intranet\Entities\Comision;
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
        $incidencies = $this->incidenciesPerDni(Hoy());

        return $this->profesores()->activos()->map(function ($profesor) use ($incidencies) {
            $dni = (string) $profesor->dni;

            if (isset($incidencies[$dni])) {
                $profesor->incidenciaFullName = $this->renderNomAmbIncidencies((string) $profesor->FullName, $incidencies[$dni]);
            }

            return $profesor;
        });
    }

    /**
     * Retorna un mapa de professorat amb incidències classificades per tipus.
     *
     * @param string $dia
     * @return array<string, array{falta: bool, activitat: bool, comissio: bool}>
     */
    private function incidenciesPerDni(string $dia): array
    {
        $incidencies = [];

        $dnisFalta = Falta::query()
            ->Dia($dia)
            ->pluck('idProfesor')
            ->map(static fn ($dni): string => (string) $dni)
            ->all();

        foreach ($dnisFalta as $dni) {
            $incidencies[$dni] = $this->mergeIncidencia($incidencies[$dni] ?? null, 'falta');
        }

        $activitats = Actividad::query()
            ->Dia($dia)
            ->where('fueraCentro', '=', 1)
            ->with('profesores:dni')
            ->get();

        foreach ($activitats as $activitat) {
            foreach ($activitat->profesores as $profesor) {
                $dni = (string) $profesor->dni;
                $incidencies[$dni] = $this->mergeIncidencia($incidencies[$dni] ?? null, 'activitat');
            }
        }

        $dnisComissio = Comision::query()
            ->Dia($dia)
            ->pluck('idProfesor')
            ->map(static fn ($dni): string => (string) $dni)
            ->all();

        foreach ($dnisComissio as $dni) {
            $incidencies[$dni] = $this->mergeIncidencia($incidencies[$dni] ?? null, 'comissio');
        }

        return $incidencies;
    }

    /**
     * Fusiona una incidència individual en l'estat acumulat d'un professor.
     *
     * @param array{falta: bool, activitat: bool, comissio: bool}|null $actual
     * @param string $tipus
     * @return array{falta: bool, activitat: bool, comissio: bool}
     */
    private function mergeIncidencia(?array $actual, string $tipus): array
    {
        $actual = $actual ?? ['falta' => false, 'activitat' => false, 'comissio' => false];

        if (array_key_exists($tipus, $actual)) {
            $actual[$tipus] = true;
        }

        return $actual;
    }

    /**
     * Renderitza el nom amb etiqueta de tipus d'incidència.
     *
     * @param string $nom
     * @param array{falta: bool, activitat: bool, comissio: bool} $incidencia
     * @return string
     */
    private function renderNomAmbIncidencies(string $nom, array $incidencia): string
    {
        $badges = [];

        if ($incidencia['falta']) {
            $badges[] = '<span class="label label-info">Absència</span>';
        }
        if ($incidencia['activitat']) {
            $badges[] = '<span class="label label-info">Activitat</span>';
        }
        if ($incidencia['comissio']) {
            $badges[] = '<span class="label label-warning">Comissió</span>';
        }

        $etiquetes = $badges === [] ? '' : ' ' . implode(' ', $badges);

        return e($nom) . $etiquetes;
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
