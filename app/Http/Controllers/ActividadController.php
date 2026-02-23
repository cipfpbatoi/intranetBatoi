<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\ModalController;
use Intranet\Presentation\Crud\ActividadCrudSchema;

use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Notifications\ActividadNotificationService;
use Intranet\Services\Document\PdfService;
use Intranet\Entities\Actividad;
use Intranet\Entities\ActividadGrupo;
use Intranet\Entities\Alumno;
use Intranet\Http\Requests\ActividadRequest;
use Intranet\Http\Requests\ValoracionRequest;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Http\Traits\Core\SCRUD;
use Intranet\Services\General\GestorService;
use Intranet\Services\Calendar\GoogleCalendarService;
use Intranet\Services\General\StateService;
use Intranet\Services\School\ActividadParticipantsService;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;


class ActividadController extends ModalController
{
    private ?GrupoService $grupoService = null;

    use Autorizacion, SCRUD, Imprimir;

    protected $perfil = 'profesor';
    protected $model = 'Actividad';
    protected $gridFields = ActividadCrudSchema::GRID_FIELDS;
    protected $formFields = ActividadCrudSchema::FORM_FIELDS;
    
    protected function search()
    {
        return Actividad::Profesor(AuthUser()->dni)
                ->where('extraescolar', 1)
                ->get();
    }


    protected function createWithDefaultValues( $default=[])
    {
        $data = new Date('tomorrow');
        return new Actividad(['extraescolar' => 1,'desde'=>$data,'hasta'=>$data,'poll' => 0,'recomanada'=>1,'complementaria'=>1,'fueraCentro'=>0,'transport'=>0]);
    }

    public function store(ActividadRequest $request)
    {
        $new = new Actividad();
        $new->fillAll($request);
        return $this->showDetalle($new);
    }

    public function update(ActividadRequest $request, $id)
    {
        Actividad::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    public function valoracion(ValoracionRequest $request)
    {
        $actividad = Actividad::findOrFail($request->idActividad);
        $actividad->desenvolupament = $request->desenvolupament;
        $actividad->valoracio = $request->valoracio;
        $actividad->dades = $request->dades;
        $actividad->aspectes = $request->aspectes;
        $actividad->recomanada = isset($request->recomanada)?1:0;
        $actividad->estado = 4;

        $actividad->save();
        return back();
    }


    /**
     * Mostra la pantalla de valoració d'una activitat.
     *
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showValue($id){
        $Actividad = Actividad::find($id);
        return view('extraescolares.showValue', compact('Actividad'));
    }

    /**
     * Mostra el formulari per omplir la valoració.
     *
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function value($id){
        $Actividad = Actividad::find($id);
        return view('extraescolares.value', compact('Actividad'));
    }

    /**
     * Genera el PDF de la valoració d'una activitat.
     *
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function printValue($id){
        $elemento = $this->class::findOrFail($id);
        $informe = 'pdf.valoracionActividad';
        return app(PdfService::class)->hazPdf($informe, $elemento, null)->stream();
    }

    /**
     * Redirigix al detall de l'activitat.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function showDetalle($id){
        return redirect()->route('actividad.detalle', ['actividad' => $id]);
    }

    /**
     * Retorna el servei de gestió de participants/coordinador d'activitats.
     */
    private function participantsService(): ActividadParticipantsService
    {
        return app(ActividadParticipantsService::class);
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    /**
     * Mostra el detall d'una activitat amb professors i grups associats.
     *
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detalle($id)
    {
        $Actividad = Actividad::with(['profesores' => function ($query) {
            $query->select('dni', 'apellido1', 'apellido2', 'nombre', 'coordinador')
                ->orderBy('apellido1')
                ->orderBy('apellido2');
        }, 'grupos:codigo,nombre'])->findOrFail($id);

        $assignedProfesores = $Actividad->profesores->pluck('dni')->all();
        $assignedGrupos = $Actividad->grupos->pluck('codigo')->all();

        // Obtenir tots els professors actius i estructurar-los en un array associatiu
        $tProfesores = app(ProfesorService::class)->activosOrdered()
            ->whereNotIn('dni', $assignedProfesores)
            ->mapWithKeys(fn($p) => [$p->dni => "$p->apellido1 $p->apellido2, $p->nombre"])
            ->toArray();

        // Llista de tots els grups disponibles
        $tGrupos = $this->grupos()->all()
            ->whereNotIn('codigo', $assignedGrupos)
            ->pluck('nombre', 'codigo')
            ->toArray();

        // Assignem professors i grups associats a l'activitat
        $sProfesores = $Actividad->profesores;
        $sGrupos = $Actividad->grupos;
        $coordinador = $sProfesores->firstWhere('coordinador', 1);

        $coordinadorNom = $coordinador
            ? trim("{$coordinador->apellido1} {$coordinador->apellido2}, {$coordinador->nombre}")
            : 'Sense assignar';

        $desdeRaw = $Actividad->getRawOriginal('desde');
        $hastaRaw = $Actividad->getRawOriginal('hasta');
        $desdeVal = $desdeRaw ? fechaString($desdeRaw, 'ca') . ' ' . hora($desdeRaw) : '-';
        $hastaVal = $hastaRaw ? fechaString($hastaRaw, 'ca') . ' ' . hora($hastaRaw) : '-';

        $tipoActividad = $Actividad->complementaria ? 'Complementaria' : 'No complementaria';
        if ($Actividad->fueraCentro) {
            $tipoActividad .= ' / Extraescolar';
        }

        return view('extraescolares.edit', compact(
            'Actividad',
            'tProfesores',
            'tGrupos',
            'sGrupos',
            'sProfesores',
            'coordinadorNom',
            'desdeVal',
            'hastaVal',
            'tipoActividad'
        ));
    }


    /**
     * Afig un grup a una activitat sense esborrar els existents.
     *
     * @param Request $request
     * @param int|string $actividad_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function altaGrupo(Request $request, $actividad_id)
    {
        $this->participantsService()->addGroup($actividad_id, (string) $request->idGrupo);
        return $this->showDetalle($actividad_id);
    }

    /**
     * Esborra un grup assignat a l'activitat.
     *
     * @param int|string $actividad_id
     * @param int|string $grupo_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function borrarGrupo($actividad_id, $grupo_id)
    {
        $this->participantsService()->removeGroup($actividad_id, (string) $grupo_id);
        return $this->showDetalle($actividad_id);
    }

    /**
     * Afig un professor participant a l'activitat.
     *
     * @param Request $request
     * @param int|string $actividad_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function altaProfesor(Request $request, $actividad_id)
    {
        $this->participantsService()->addProfesor($actividad_id, (string) $request->idProfesor);
        return $this->showDetalle($actividad_id);
    }

    /**
     * Esborra un professor participant.
     * Si era el coordinador, en promou un altre per garantir que n'hi haja exactament un.
     *
     * @param int|string $actividad_id
     * @param string $profesor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function borrarProfesor($actividad_id, $profesor_id)
    {
        if (!$this->participantsService()->removeProfesor($actividad_id, $profesor_id)) {
            Alert::info('No es pot donar de baixa el últim profesor');
            return back();
        }

        return $this->showDetalle($actividad_id);
    }

    /**
     * Assigna el coordinador de l'activitat.
     * Primer reinicia tots els coordinadors a 0 i després marca el professor seleccionat.
     *
     * @param int|string $actividad_id
     * @param string $profesor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function coordinador($actividad_id, $profesor_id)
    {
        if (!$this->participantsService()->assignCoordinator($actividad_id, $profesor_id)) {
            Alert::warning('El professor seleccionat no participa en l’activitat.');
            return back();
        }

        return $this->showDetalle($actividad_id);
    }

    /**
     * Notifica a professorat afectat i tutors dels grups de l'activitat.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notify($id)
    {
        $actividad = Actividad::findOrFail($id);
        $coordinador = app(ProfesorService::class)->find((string) $actividad->Creador());
        if (!$coordinador) {
            Alert::warning('No hi ha cap coordinador assignat per a esta activitat.');
            return back();
        }

        app(ActividadNotificationService::class)->notifyActivity($actividad, $coordinador);

        return back();
    }

    /**
     * Genera/mostra l'autorització de menors i crea registres si encara no existixen.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function autorizacion($id)
    {
        $grups = [];
        $actividad = Actividad::findOrFail($id);
        $grups = ActividadGrupo::where('idActividad', $id)->pluck('idGrupo')->toArray();
        $todos = Alumno::join('alumnos_grupos', 'idAlumno', '=', 'nia')
                ->select('alumnos.*', 'idGrupo')
                ->QGrupo($grups)
                ->Menor($actividad->desde)
                ->orderBy('idGrupo')
                ->orderBy('apellido1')
                ->orderBy('apellido2')
                ->get();
        if ($actividad->menores()->count()==0){
            foreach ($todos as $alumno){
                $actividad->menores()->attach($alumno->nia,['autorizado' => 0]);
            }
        }
        if ($todos->count()){
            $pdf = app(PdfService::class)->hazPdf('pdf.autoritzacioMenors', $todos, $actividad, 'portrait');
            return $pdf->stream();
        }
        Alert::info('No hi han menors');
        return back();
    }

    /**
     * Mostra la pantalla de control d'autoritzacions de menors.
     *
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function autorize($id){
        $actividad = Actividad::findOrFail($id);
        if ($actividad->menores()->count()) {
            return view('extraescolares.autorizados', compact('actividad'));
        }
        Alert::danger('No has imprés cap autorització');
        return back();
    }

    /**
     * Inicialitza la botonera del grid i perfil.
     *
     * @return void
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBothBoton('actividad.detalle', ['where' => ['estado', '<', '5']]);
        $this->panel->setBothBoton('actividad.edit', ['where' => ['estado', '<', '2']]);
        $this->panel->setBothBoton('actividad.init', ['where' => ['estado', '==', '0']]);
        $this->panel->setBothBoton('actividad.notification', ['where' => ['estado', '>', '0', 'estado', '<', '4', 'coord', '==', '1','desde','posterior',Hoy()]]);
        $this->panel->setBothBoton('actividad.autorizacion', ['where' => ['estado', '>', '0','estado','<','4','desde','posterior',Hoy()]]);
        $this->panel->setBoton('grid',new BotonImg('actividad.pdfVal', ['img'=>'fa-file-pdf-o','where' => ['estado', '==', '4','hasta','anterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.showVal', ['img'=>'fa-eye-slash','where' => ['estado', '==', '4','hasta','anterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.autorize', ['img'=>'fa-filter','where' => ['estado', '>', '0','estado','<=','3','desde','posterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.value', ['img'=>'fa-eyedropper','where' => ['estado', '>=', '3','hasta','anterior',Hoy(),'coord','==',1]]));
        $this->panel->setBoton('grid', new BotonImg('actividad.delete', [
            'where' => ['estado', '<', '2'],
            'data-confirm' => 'Segur que vols eliminar esta activitat?',
        ]));
        $this->panel->setBoton('profile', new BotonIcon('actividad.delete', [
            'class' => 'btn-danger',
            'where' => ['estado', '<', '2'],
            'data-confirm' => 'Segur que vols eliminar esta activitat?',
        ]));
        $this->panel->setBoton('grid', new BotonImg('actividad.ics', ['img' => 'fa-calendar', 'where' => ['desde', 'posterior', Date::yesterday()]]));
    }

    /**
     * Autoritza activitats en estat 1 i, si hi ha credencials, les exporta a calendari.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function autorizar()
    {
        $activitats = Actividad::where('estado', '1')->get();
        if (file_exists(storage_path(env('services.calendar.calendarCredentialsPath')))) {
            $gC = new GoogleCalendarService();
            foreach ($activitats as $activitat){
                $assistents = $activitat->profesores()->select('email')->get()->toArray();
                $gC->addEvent(
                    $activitat->name,
                    $activitat->descripcion,
                    $activitat->desde,
                    $activitat->hasta,
                    $assistents
                );
            }
            $gC->saveEvents();
        }
        StateService::makeAll($activitats, 2);
        return back();
    }


    /**
     * Accepta l'activitat incrementant estat i sincronitzant calendari extern.
     *
     * @param int|string $id
     * @param bool $redirect
     * @return mixed
     */
    protected function accept($id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        if (file_exists(storage_path(env('services.calendar.calendarCredentialsPath')))) {
            $gC = new GoogleCalendarService();
            $activitat = Actividad::find($id);
            $assistents = $activitat->profesores()->select('email')->get()->toArray();
            $gC->addEvent(
                $activitat->name,
                $activitat->descripcion,
                $activitat->desde,
                $activitat->hasta,
                $assistents
            );
            $gC->saveEvents();
        }
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->putEstado($iniSta+1);
        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }

    /**
     * Imprimeix el llistat d'autoritzats.
     *
     * @return mixed
     */
    public function printAutoritzats(){
        return $this->imprimir('extraescolars');
    }

    /**
     * Marca l'activitat com a tramitada en ITACA.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function itaca($id)
    {
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 5;
        $elemento->save();
        return $this->follow(4, 5);
    }

    /**
     * Alterna l'estat d'autorització d'un alumne menor.
     *
     * @param string $nia
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function menorAuth($nia,$id){
        $actividad = Actividad::findOrFail($id);
        $alumno = $actividad->menores()->where('nia', $nia)->first();
        if (!$alumno) {
            Alert::warning('L’alumne no està associat a esta activitat.');
            return back();
        }

        if ($alumno->pivot->autorizado){
            $autorizado = 0;
        } else {
            $autorizado = 1;
        }
        $actividad->menores()->updateExistingPivot($nia,['autorizado' => $autorizado]);
        return view('extraescolares.autorizados',compact('actividad'));
    }

    /**
     * Renderitza el document associat a l'activitat amb GestorService.
     *
     * @param int|string $id
     * @return mixed
     */
    public function gestor($id)
    {
        return (new GestorService(Actividad::findOrFail($id)))->render();
    }
}
