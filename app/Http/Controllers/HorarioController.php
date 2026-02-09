<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\BaseController;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Intranet\UI\Botones\BotonImg;
use Illuminate\Support\Facades\Session;
use Intranet\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Mail;

class HorarioController extends IntranetController
{

    protected $model = 'Horario';
    protected $perfil = 'profesor';
    protected $gridFields = ['XModulo','XOcupacion' ,'dia_semana', 'desde', 'aula'];
    protected $modal = true;


    private function getJsonFromFile($dni){
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json') && $fichero = Storage::disk('local')->get('/horarios/'.$dni.'.json')) {
            return json_decode($fichero);
        }
        return null;
    }

    private function changeHorary($dni,$cambios){
        foreach ($cambios as $cambio) {

            $de=explode("-",$cambio->de);
            $a=explode("-", $cambio->a);

            $horario = Horario::dia($de[1])->orden($de[0])->Profesor($dni)->first();
            if ($horario){
                $horario->dia_semana = $a[1];
                $horario->sesion_orden = $a[0];
                $horario->save();
            } else {
                Alert::info("Horari".$de[1].' '.$de[0]." del profesor $dni no trobat");
            }

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

        foreach (Profesor::select('dni')->Activo()->get() as $profe) {
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
                'profesor' => Profesor::find($dni),
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

    protected function sendAcceptationEmail(string $dni, array $data, string $id): void
    {
        $profesor = Profesor::find($dni);
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
        $horario = Horario::HorarioSemanal($id);
        $profesor = Profesor::find($id);
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

    /**
     * @param $idProfesor
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function modificarHorario($idProfesor){
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        Session::put('horarioProfesor',$idProfesor);
        $this->titulo = ['quien' => Profesor::find($idProfesor)->fullName]; // paràmetres per al titol de la vista
        $this->iniBotones();
        return $this->grid(Horario::Profesor($idProfesor)->get());
    }
}
