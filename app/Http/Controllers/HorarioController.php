<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\ModalController;
use Intranet\Http\Requests\HorarioUpdateRequest;
use Intranet\Presentation\Crud\HorarioCrudSchema;

use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Intranet\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Mail;
use Intranet\Services\UI\FormBuilder;

/**
 * Gestiona el canvi temporal d'horaris i la revisió de propostes.
 */
class HorarioController extends ModalController
{

    protected $model = 'Horario';
    protected $perfil = 'profesor';
    protected $gridFields = HorarioCrudSchema::GRID_FIELDS;
    protected $formFields = HorarioCrudSchema::FORM_FIELDS;
    private ?HorarioService $horarioService = null;
    private ?ProfesorService $profesorService = null;

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function getJsonFromFile($dni){
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json') && $fichero = Storage::disk('local')->get('/horarios/'.$dni.'.json')) {
            return json_decode($fichero);
        }
        return null;
    }

    /**
     * Aplica els canvis d'horari utilitzant la posició original com a referència.
     *
     * @param string $dni
     * @param iterable|null $cambios
     */
    private function changeHorary(string $dni, $cambios): void
    {
        if (!is_iterable($cambios)) {
            return;
        }
        $horarios = $this->horarios()->byProfesor((string) $dni)->sortBy('id');
        $horariosById = [];
        $horariosByCell = [];

        foreach ($horarios as $horario) {
            $horariosById[(string) $horario->id] = $horario;
            $cell = $horario->sesion_orden . '-' . $horario->dia_semana;
            if (!isset($horariosByCell[$cell])) {
                $horariosByCell[$cell] = $horario;
            }
        }

        foreach ($cambios as $cambio) {
            $from = is_array($cambio) ? ($cambio['de'] ?? null) : ($cambio->de ?? null);
            $to = is_array($cambio) ? ($cambio['a'] ?? null) : ($cambio->a ?? null);
            $id = is_array($cambio) ? ($cambio['id'] ?? null) : ($cambio->id ?? null);

            if (!$from || !$to) {
                continue;
            }

            $horario = $id ? ($horariosById[(string) $id] ?? null) : ($horariosByCell[(string) $from] ?? null);
            if (!$horario) {
                $deParts = explode('-', (string) $from, 2);
                $dia = $deParts[1] ?? '';
                $sesion = $deParts[0] ?? '';
                Alert::info("Horari" . $dia . ' ' . $sesion . " del profesor $dni no trobat");
                continue;
            }

            $aParts = explode('-', (string) $to, 2);
            if (count($aParts) < 2) {
                continue;
            }

            $horario->sesion_orden = (int) $aParts[0];
            $horario->dia_semana = (string) $aParts[1];
            $horario->save();
        }
    }
    private function saveCopy($dni,$data){
        if (! Storage::disk('local')->exists('/horarios/horariosCambiados/'.$dni.'.json')) {
            Storage::disk('local')->put('/horarios/horariosCambiados/' . $dni . '.json', json_encode($data));
        }

    }

    public function changeTable($dni,$redirect=true){
        $correcto = false;
        if ($data = $this->getJsonFromFile($dni)){
                switch ($data->estado) {
                    case "Aceptado":
			            $this->saveCopy($dni,$data);
                        $this->changeHorary($dni,$data->cambios);

                        $data->estado="Guardado";
                        $data->cambios=[];
                        if (Storage::disk('local')->put('/horarios/'.$dni.'.json', json_encode($data))) {
                            $correcto = true;
                        }
                        else {
                            Alert::warning("Horari amb dni $dni modificat però no s\'ha pogut guardar el fitxer");
                        }
                        break;
                    case "Guardado":
                        Alert::info("Horari amb dni $dni ja està guardat");
                        break;
                    default:
                        Alert::warning("Horari amb dni $dni no està aceptat");
                }
        } else {
            Alert::danger("Horari amb dni $dni no té canvis");
        }

        if ($redirect) {
            return back();
        }
        else {
            return $correcto;
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function changeTableAll(){
        $correctos = 0;

        foreach ($this->profesores()->activos() as $profe) {
            $correctos += $this->changeTable($profe->dni,false);
        }
        Alert::success("He fet $correctos canvis d'horaris");
        return back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changeIndex() {
        return view('horario.change');
    }

    public function propuestas(Request $request)
    {
        $disk = Storage::disk('local');
        $files = $disk->allFiles('/horarios');
        $propuestas = [];

        foreach ($files as $file) {
            if (str_contains($file, 'horariosCambiados/')) {
                continue;
            }
            if (!str_ends_with($file, '.json')) {
                continue;
            }
            $parts = explode('/', trim($file, '/'));
            if (count($parts) < 3) {
                continue;
            }
            $dni = $parts[count($parts) - 2] ?? null;
            if (!$dni || $dni === 'horarios') {
                continue;
            }
            $raw = $disk->get($file);
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                continue;
            }

            $propuestas[] = [
                'dni' => $dni,
                'id' => $data['id'] ?? basename($file, '.json'),
                'profesor' => $this->profesores()->find((string) $dni),
                'estado' => $data['estado'] ?? 'Pendiente',
                'obs' => $data['obs'] ?? '',
                'motiu_rebuig' => $data['motiu_rebuig'] ?? '',
                'fecha_inicio' => $data['fecha_inicio'] ?? '',
                'fecha_fin' => $data['fecha_fin'] ?? '',
                'cambios' => is_array($data['cambios'] ?? null) ? $data['cambios'] : [],
            ];
        }

        $estado = $request->get('estado', 'Pendiente');
        if ($estado !== 'Todos') {
            $propuestas = array_values(array_filter($propuestas, function ($p) use ($estado) {
                return ($p['estado'] ?? 'Pendiente') === $estado;
            }));
        }

        return view('horario.propuestas', [
            'propuestas' => $propuestas,
            'estado' => $estado,
        ]);
    }

    public function aceptarPropuesta($dni, $id)
    {
        $disk = Storage::disk('local');
        $path = '/horarios/' . $dni . '/' . $id . '.json';

        if (!$disk->exists($path)) {
            Alert::warning("No hi ha proposta per al professor $dni");
            return back();
        }

        $raw = $disk->get($path);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            Alert::warning("La proposta del professor $dni no es valida");
            return back();
        }

        $data['estado'] = 'Aceptado';
        $data['updated_at'] = date('Y-m-d H:i:s');
        $disk->put($path, json_encode($data));

        app(NotificationService::class)->send($dni, "S'ha acceptat la teua proposta de canvi d'horari.", '/horario/canvi-horari-temporal?proposta=' . $id);
        $this->sendAcceptationEmail($dni, $data, $id);
        Alert::success("Proposta del professor $dni acceptada");
        return back();
    }

    public function rebutjarProposta(Request $request, $dni, $id)
    {
        $disk = Storage::disk('local');
        $path = '/horarios/' . $dni . '/' . $id . '.json';

        if (!$disk->exists($path)) {
            Alert::warning("No hi ha proposta per al professor $dni");
            return back();
        }

        $raw = $disk->get($path);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            Alert::warning("La proposta del professor $dni no es valida");
            return back();
        }

        $motiu = trim((string) $request->get('motiu', ''));
        if ($motiu === '') {
            Alert::warning("Has d'indicar un motiu de rebuig");
            return back();
        }

        $data['estado'] = 'Rebutjat';
        $data['motiu_rebuig'] = $motiu;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $disk->put($path, json_encode($data));

        app(NotificationService::class)->send($dni, "S'ha rebutjat la teua proposta de canvi d'horari. Motiu: $motiu", '/horario/canvi-horari-temporal?proposta=' . $id);
        Alert::success("Proposta del professor $dni rebutjada");
        return back();
    }

    public function esborrarProposta($dni, $id)
    {
        $disk = Storage::disk('local');
        $path = '/horarios/' . $dni . '/' . $id . '.json';

        if (!$disk->exists($path)) {
            Alert::warning("No hi ha proposta per al professor $dni");
            return back();
        }

        $disk->delete($path);
        Alert::success("Proposta del professor $dni esborrada");
        return back();
    }

    protected function sendAcceptationEmail(string $dni, array $data, string $id): void
    {
        $profesor = $this->profesores()->find($dni);
        if (!$profesor || empty($profesor->email)) {
            return;
        }

        $lines = [];
        $cambios = is_array($data['cambios'] ?? null) ? $data['cambios'] : [];
        foreach ($cambios as $cambio) {
            if (!isset($cambio['de'], $cambio['a'])) {
                continue;
            }
            $lines[] = "- {$cambio['de']} -> {$cambio['a']}";
        }

        $body = "S'ha acceptat la teua proposta de canvi d'horari.\n";
        $body .= "Professor/a: {$profesor->fullName}\n";
        $body .= "Dates: ".($data['fecha_inicio'] ?? '')." a ".($data['fecha_fin'] ?? '')."\n";
        if (!empty($data['obs'])) {
            $body .= "Observacions: {$data['obs']}\n";
        }
        $body .= "Canvis acceptats:\n";
        $body .= empty($lines) ? "- (sense canvis)\n" : implode("\n", $lines)."\n";
        $body .= "Pots consultar la proposta ací: ".url('/horario/canvi-horari-temporal?proposta='.$id)."\n";

        Mail::raw($body, function ($message) use ($profesor) {
            $message->to($profesor->email, $profesor->fullName)
                ->subject('Proposta d\'horari acceptada');
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function horarioCambiar($id = null){
        if ($id == null) {
            $id = AuthUser()->id;
        }
        $horario = $this->horarios()->semanalByProfesor((string) $id);
        $profesor = $this->profesores()->find((string) $id);
        return view('horario.profesor-cambiar', compact('horario', 'profesor'));
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera([],['edit']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(){
        return $this->modificarHorario(Session::get('horarioProfesor'));
    }

    public function update(HorarioUpdateRequest $request, $id)
    {
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * @param $idProfesor
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function modificarHorario($idProfesor){
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        Session::put('horarioProfesor',$idProfesor);
        $this->titulo = ['quien' => $this->profesores()->find((string) $idProfesor)->fullName]; // paràmetres per al titol de la vista
        $this->iniBotones();
        return $this->panel->render(
            $this->horarios()->byProfesor((string) $idProfesor),
            $this->titulo,
            'intranet.indexModal',
            new FormBuilder($this->createWithDefaultValues(), $this->formFields)
        );
    }

}
