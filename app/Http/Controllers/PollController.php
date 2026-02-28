<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Poll\PollWorkflowService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Exports\PollResultsExport;
use Maatwebsite\Excel\Facades\Excel;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\UI\AppAlert as Alert;

class   PollController extends IntranetController
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

    protected function preparaEnquesta($id)
    {
        $data = $this->polls()->prepareSurvey($id, AuthUser());
        if (!$data) {
            Alert::danger("Enquesta no trobada");
            return back();
        }

        $poll = $data['poll'];
        $quests = $data['quests'];
        if ($quests) {
            return view('poll.enquesta', compact('quests', 'poll'));
        }


        Alert::info("Ja has omplit l'enquesta");
        return redirect('home');
    }

    protected function guardaEnquesta(Request $request, $id)
    {
        if (!$this->polls()->saveSurvey($request, $id, AuthUser())) {
            Alert::danger("Enquesta no trobada");
            return back();
        }

        Alert::info('Enquesta emplenada amb exit');
        return redirect('home');
    }



    public function lookAtMyVotes($id)
    {
        $data = $this->polls()->myVotes($id);
        if (!$data) {
            Alert::danger("Enquesta no trobada");
            return back();
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
        $options = $data['options'];

        return view(
            'poll.show',
            compact(
                'myVotes',
                'poll',
                'options_numeric',
                'options_text',
                'myGroupsVotes',
                'options'
            )
        );

    }



    public function lookAtAllVotes($id)
    {
        $data = $this->polls()->allVotes($id, $this->grupos());
        if (!$data) {
            Alert::danger("Enquesta no trobada");
            return back();
        }

        $poll = $data['poll'];
        $votes = $data['votes'];
        $options_numeric = $data['options_numeric'];
        $hasVotes = $data['hasVotes'];
        $stats = $data['stats'];

        return Excel::download(
            new PollResultsExport($poll, $votes, $options_numeric, $hasVotes, $stats),
            'resultats_enquesta.xlsx'
        );
        //return view('poll.allResolts', compact('votes', 'poll', 'options_numeric'));
    }
}
