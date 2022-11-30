<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\Vote;
use Response;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Styde\Html\Facades\Alert;

class   PollController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    protected $model = 'Poll';
    protected $gridFields = [ 'id','title','state'];

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("poll.create",inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('poll.edit',inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('poll.delete',inRol('qualitat')));
        $this->panel->setBoton('grid',new BotonImg('poll.chart',array_merge(['img' => 'fa-bar-chart'],inRol('qualitat'))));
        $this->panel->setBoton('grid',new BotonImg('poll.show',['img' =>'fa-eye']));
    }

    private function userKey($poll):String
    {
        $key = $poll->keyUser;
        if ($poll->anonymous) {
            return hash('md5', AuthUser()->$key);
        }
        return AuthUser()->$key;
    }


    protected function preparaEnquesta($id){
        $poll = Poll::find($id);
        $modelo = $poll->modelo;
        $quests = $modelo::loadPoll($this->loadPreviousVotes($poll));

        if ($quests) {
            return view('poll.enquesta', compact('quests', 'poll'));
        }


        Alert::info("Ja has omplit l'enquesta");
        return redirect('home');
    }

    private function loadPreviousVotes($poll){
        return hazArray(Vote::where('user_id','=', $this->userKey($poll))
            ->where('idPoll', $poll->id)
            ->get(),'idOption1','idOption1');
    }

    protected function guardaEnquesta(Request $request,$id){

        $poll = Poll::find($id);
        $modelo = $poll->modelo;
        $quests = $modelo::loadPoll($this->loadPreviousVotes($poll));

        foreach ($poll->Plantilla->options as $question => $option){
            $i=0;
            foreach ($quests as $quest) {
                if (isset($quest['option2'])) {
                    foreach ($quest['option2'] as $profesores)
                        foreach ($profesores as $dni) {
                            $i++;
                            $field = 'option' . ($question + 1) . '_' . $i;
                            $this->guardaVoto($poll, $option, $quest['option1']->id, $dni, $request->$field);
                        }
                } else {

                    $field = 'option' . ($question + 1) . '_' . $quest['option1']->id;
                    $this->guardaVoto($poll, $option, $quest['option1']->id, null, $request->$field);
                }
            }
        }
        Alert::info('Enquesta emplenada amb exit');
        return redirect('home');
    }

    private function guardaVoto($poll,$option,$option1,$option2,$value){

        if ($value != '' && $value != '0'){
            $vote = new Vote();
            $vote->idPoll = $poll->id;
            $vote->user_id = $poll->anonymous ? hash('md5', AuthUser()->id) : AuthUser()->id;
            $vote->option_id = $option->id;
            $vote->idOption1 = $option1;
            $vote->idOption2 = $option2;
            if ($option->scala == 0) {
                $vote->text = $value;
            }
            else {
                $vote->value = voteValue($option2, $value);
            }
            $vote->save();
        }
    }



    public function lookAtMyVotes($id)
    {
        $poll = Poll::find($id);
        $modelo = $poll->modelo;
        $myVotes = $modelo::loadVotes($id);
        if ($myVotes) {
            $myGroupsVotes = $modelo::loadGroupVotes($id);
            $options_numeric = $poll->Plantilla->options->where('scala', '>', 0);
            $options_text = $poll->Plantilla->options->where('scala', '=', 0);
            $options = $poll->Plantilla->options;
            return view('poll.show', compact('myVotes', 'poll', 'options_numeric',
                'options_text', 'myGroupsVotes','options'));
        }
        Alert::info("L'enquesta no ha estat realitzada encara");
        return back();

    }



    public function lookAtAllVotes($id)
    {
        $poll = Poll::find($id);
        $modelo = $poll->modelo;
        $options_numeric = $poll->Plantilla->options->where('scala', '>', 0);
        $allVotes = Vote::allNumericVotes($id)->get();
        $option1 = $allVotes->GroupBy(['idOption1', 'option_id']);
        $option2 = $allVotes->GroupBy(['idOption2', 'option_id']);
        $this->initValues($votes,$options_numeric);
        $votes['all'] = $allVotes->GroupBy('option_id');
        $modelo::aggregate($votes,$option1,$option2);


        return view('poll.allResolts',compact('votes','poll','options_numeric'));
    }



    private function initValues(&$votes,$options){
        $grupos = Grupo::all();
        $ciclos = Ciclo::all();
        $departamentos = Departamento::all();
        foreach ($options as $value){
            foreach ($grupos as $grupo) {
                $votes['grup'][$grupo->codigo][$value->id] = collect();
            }
            foreach ($ciclos as $ciclo) {
                $votes['cicle'][$ciclo->id][$value->id] = collect();
            }
            foreach ($departamentos as $departamento) {
                $votes['departament'][$departamento->id][$value->id] = collect();
            }
        }
    }
}
