<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Tutoria\TutoriaFeedbackService;
use Intranet\Http\Controllers\Core\IntranetController;

use Response;
use Intranet\Entities\Tutoria;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Presentation\Crud\TutoriaCrudSchema;
use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Support\Facades\Session;

/**
 * Gestiona les tutories, el feedback dels tutors i el seguiment d'Orientació.
 */
class TutoriaController extends IntranetController
{
    private ?GrupoService $grupoService = null;
    private ?TutoriaFeedbackService $feedbackService = null;

    protected $perfil = 'profesor';
    protected $model = 'Tutoria';
    protected $gridFields = TutoriaCrudSchema::GRID_FIELDS;

    /**
     * Crea el controlador amb els serveis de grups i feedback substituïbles.
     */
    public function __construct(
        ?GrupoService $grupoService = null,
        ?TutoriaFeedbackService $feedbackService = null
    )
    {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->feedbackService = $feedbackService;
    }

    /**
     * Resol el servei de seguiment del feedback de tutories.
     */
    private function feedback(): TutoriaFeedbackService
    {
        if ($this->feedbackService === null) {
            $this->feedbackService = app(TutoriaFeedbackService::class);
        }

        return $this->feedbackService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    public function index()
    {
        Session::forget('redirect');
        if (esRol(AuthUser()->rol, config('roles.rol.orientador'))) {
            return $this->indexTutoria();
        }

        if ($grupo = $this->grupos()->firstByTutor(AuthUser()->dni)) {
            $this->titulo = ['que' => $grupo->nombre];
            return parent::index();
        }

        Alert::danger('No eres tutor de cap grup');
        return back();
    }

    public function search()
    {
        return Tutoria::where('desde', '>=', config('curso.evaluaciones.1')[0])->get();
    }

    public function detalle($id)
    {
        return redirect()->route('tutoriagrupo.indice', ['id' => $id]);
    }

    public function indexTutoria()
    {
        $todos = Tutoria::all();
        $this->feedback()->attachProgress($todos);
        $this->titulo = ['que' => __('messages.menu.Orientacion')];
        $this->iniTutBotones();
        $this->panel->setRejilla(TutoriaCrudSchema::ORIENTACION_GRID_FIELDS);
        return $this->grid($todos, false);
    }

    public function anexo($id)
    {
        $grupo = $this->grupos()->firstByTutor(AuthUser()->dni)?->codigo;
        if ($grupo === null) {
            Alert::danger('No eres tutor de cap grup');
            return back();
        }
        $elemento = TutoriaGrupo::where('idTutoria', '=', $id)->where('idGrupo', '=', $grupo)->first();
        if (isset($elemento->idGrupo)) {
            return redirect()->route('tutoriagrupo.edit', ['id' => $elemento->id]);
        }
        return redirect()->route('tutoriagrupo.create', ['tutoria' => $id, 'grupo' => $grupo ]);
    }

    protected function iniTutBotones()
    {
        $this->panel->setBotonera(['create'], ['edit', 'delete', 'document', 'detalle']);
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['document', 'anexo']);
    }

    

}
