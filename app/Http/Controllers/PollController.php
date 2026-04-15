<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Poll\PollWorkflowService;
use Intranet\Entities\Grupo;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Exports\PollResultsExport;
use Maatwebsite\Excel\Facades\Excel;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Controlador d'enquestes per a consultes i resultats.
 */
class PollController extends IntranetController
{
    private ?GrupoService $grupoService = null;
    private ?PollWorkflowService $pollWorkflowService = null;

    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    protected $model = 'Poll';
    protected $gridFields = [ 'id','title','state'];

    public function __construct(?GrupoService $grupoService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function polls(): PollWorkflowService
    {
        if ($this->pollWorkflowService === null) {
            $this->pollWorkflowService = app(PollWorkflowService::class);
        }

        return $this->pollWorkflowService;
    }

    /**
     * Retorna el curs (1 o 2) de l'usuari autenticat basant-se en el seu grup.
     * Per a alumnes, el curs prové del seu grup assignat.
     * Per a professors/tutors, prové del grup que tutoritzen.
     * Retorna null si no es pot determinar el curs.
     */
    protected function getUserCurs(): ?int
    {
        $user = AuthUser();
        if (isset($user->nia)) {
            $grupo = $user->Grupo->first();
            return $grupo ? (int) $grupo->curso : null;
        }
        $grupoCodi = $user->GrupoTutoria;
        if ($grupoCodi) {
            $grupo = Grupo::find($grupoCodi);
            return $grupo ? (int) $grupo->curso : null;
        }
        return null;
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("poll.create", inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('poll.edit', inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('poll.delete', inRol('qualitat')));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'poll.chart',
                array_merge(['img' => 'fa-bar-chart'], inRol('qualitat'))
            )
        );
        $this->panel->setBoton('grid', new BotonImg('poll.show', ['img' =>'fa-eye']));
    }

    /**
     * Prepara una enquesta per a l'usuari actual.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function preparaEnquesta($id)
    {
        $data = $this->polls()->prepareSurvey($id, AuthUser());
        if (!$data) {
            throw new NotFoundDomainException('Enquesta no trobada', ['poll_id' => $id]);
        }

        $poll = $data['poll'];
        $quests = $data['quests'];
        if ($quests) {
            return view('poll.enquesta', compact('quests', 'poll'));
        }


        Alert::info("Ja has omplit l'enquesta");
        return redirect('home');
    }

    /**
     * Desa una enquesta contestada.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function guardaEnquesta(Request $request, $id)
    {
        if (!$this->polls()->saveSurvey($request, $id, AuthUser())) {
            throw new NotFoundDomainException('Enquesta no trobada', ['poll_id' => $id]);
        }

        Alert::info('Enquesta emplenada amb exit');
        return redirect('home');
    }



    /**
     * Mostra els vots de l'usuari per a una enquesta.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function lookAtMyVotes($id)
    {
        $data = $this->polls()->myVotes($id);
        if (!$data) {
            throw new NotFoundDomainException('Enquesta no trobada', ['poll_id' => $id]);
        }

        if (!$data['myVotes']) {
            Alert::info("L'enquesta no ha estat realitzada encara");
            return back();
        }

        $poll = $data['poll'];
        $myVotes = $data['myVotes'];
        $myGroupsVotes = $data['myGroupsVotes'];
        $options_numeric = $data['options_numeric'];
        $options_text = $data['options_text'];
        $options_select = $data['options_select'];
        $options = $data['options'];

        return view(
            'poll.show',
            compact(
                'myVotes',
                'poll',
                'options_numeric',
                'options_text',
                'options_select',
                'myGroupsVotes',
                'options'
            )
        );

    }



    /**
     * Exporta els resultats d'una enquesta.
     *
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function lookAtAllVotes($id)
    {
        $data = $this->polls()->allVotes($id, $this->grupos());
        if (!$data) {
            throw new NotFoundDomainException('Enquesta no trobada', ['poll_id' => $id]);
        }

        $poll = $data['poll'];
        $votes = $data['votes'];
        $options_numeric = $data['options_numeric'];
        $options_select = $data['options_select'];
        $hasVotes = $data['hasVotes'];
        $stats = $data['stats'];
        $selectStats = $data['selectStats'];
        $selectHasVotes = $data['selectHasVotes'];

        return Excel::download(
            new PollResultsExport(
                $poll,
                $votes,
                $options_numeric,
                $options_select,
                $hasVotes,
                $stats,
                $selectStats,
                $selectHasVotes
            ),
            'resultats_enquesta.xlsx'
        );
        //return view('poll.allResolts', compact('votes', 'poll', 'options_numeric'));
    }
}
