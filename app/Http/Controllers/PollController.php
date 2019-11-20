<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Fct;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Poll\Option;
use Response;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Styde\Html\Facades\Alert;

class PollController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    protected $model = 'Poll';
    protected $gridFields = [ 'id','title','actiu'];

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("poll.create",inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('poll.edit',inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('poll.delete',inRol('qualitat')));
        $this->panel->setBoton('grid',new BotonImg('poll.chart',array_merge(['img' => 'fa-bar-chart'],inRol('qualitat'))));
        $this->panel->setBoton('grid',new BotonImg('poll.show',['img' =>'fa-eye']));
    }
    
    protected function preparaEnquesta($id){
        $votes = Vote::where('user_id','=', hash('md5',AuthUser()->nia))
                ->whereIn('option_id', hazArray(Option::where('ppoll_id',$id)->get(),'id'))
                ->count();
        if ($votes == 0){
            $poll = Poll::find($id);
            $modulos = $this->calculateAnswers($poll->que);
            return view('poll.enquesta',compact('modulos','poll'));
        }

        Alert::info("Ja has omplit l'enquesta");
        return redirect('home');
    }

    public function lookAtMyVotes($id){
        $poll = Poll::find($id);
        $options_numeric = $poll->options->where('scala','>',0);
        $options_text = $poll->options->where('scala','=',0);
        $myVotes = [];
        $myGroupsVotes = [];
        foreach (Modulo_grupo::misModulos() as $modulo){
            $myVotes[$modulo->ModuloCiclo->Modulo->literal][$modulo->Grupo->codigo] = Vote::myVotes($id,$modulo->id)->get();
        }
        foreach (Grupo::misGrupos()->get() as $grup){
            $myGroupsVotes[$grup->codigo] = Vote::myGroupVotes($id,$grup->codigo)->get();
        }
        if (count($myVotes)) return view('poll.teacherResolts',compact('myVotes','poll','options_numeric','options_text','myGroupsVotes'));
        Alert::info("L'enquesta no ha estat realitzada encara");
        return back();
    }
    public function lookAtAllVotes($id)
    {
        $poll = Poll::find($id);
        $options_numeric = $poll->options->where('scala', '>', 0);
        $allVotes = Vote::allNumericVotes($id)->get();
        $moduloVotes = $allVotes->GroupBy(['idModuloGrupo', 'option_id']);
        $personalVotes = $allVotes->GroupBy(['idProfesor', 'option_id']);
        $this->initValues($votes,$options_numeric);
        $votes['all'] = $allVotes->GroupBy('option_id');

        foreach (Grupo::all() as $grupo) {
            foreach ($grupo->Modulos as $modulo)
                if (isset($moduloVotes[$modulo->id])) {
                    foreach ($moduloVotes[$modulo->id] as $key => $optionVotes) {
                        foreach ($optionVotes as $optionVote) {
                            $votes['grup'][$grupo->codigo][$key]->push($optionVote);
                            $votes['cicle'][$modulo->ModuloCiclo->idCiclo][$key]->push($optionVote);
                        }
                    }
                }
        }
        foreach (Departamento::all() as $departamento) {
            foreach ($departamento->Profesor as $profesor)
                if (isset($personalVotes[$profesor->dni])) {
                    foreach ($personalVotes[$profesor->dni] as $key => $optionVotes)
                        foreach ($optionVotes as $optionVote) {
                            $votes['departament'][$departamento->id][$key]->push($optionVote);
                        }
                }
        }
        return view('poll.allResolts',compact('votes','poll','options_numeric'));
    }

    private function initValues(&$votes,$options){
        $grupos = Grupo::all();
        $ciclos = Ciclo::all();
        $departamentos = Departamento::all();
        foreach ($options as $key => $value){
            foreach ($grupos as $grupo) $votes['grup'][$grupo->codigo][$value->id] = collect();
            foreach ($ciclos as $ciclo) $votes['cicle'][$ciclo->id][$value->id] = collect();
            foreach ($departamentos as $departamento) $votes['departament'][$departamento->id][$value->id] = collect();
        }
    }


    protected function guardaEnquesta(Request $request,$id){
        $poll = Poll::find($id);
        $modulos = $this->calculateAnswers($request->que);
        foreach ($poll->options as $question => $option){
            $profe=0;
            foreach ($modulos as $modulo)
                foreach ($modulo['profesores'] as $profesores)
                    foreach ($profesores as $dni){
                        $profe++;
                        $value = 'option'.($question+1).'_'.$profe;
                        $vote = new Vote();
                        $vote->user_id = $cifrar?hash('md5',AuthUser()->id):AuthUser()->id;
                        $vote->option_id = $option->id;
                        $vote->idModuloGrupo = $modulo['modulo']->id;
                        $vote->idProfesor = $dni;
                        if ($option->scala == 0) $vote->text = $request->$value;
                        else $vote->value = $request->$value;
                        $vote->save();
                    }
        }
        Alert::info('Enquesta emplenada amb exit');
        return redirect('home');
    }

    private function guardaVoto($){
        $vote = new Vote();
        $vote->user_id = $cifrar?hash('md5',AuthUser()->id):AuthUser()->id;
        $vote->option_id = $option->id;
        $vote->idModuloGrupo = $modulo['modulo']->id;
        $vote->idProfesor = $dni;
        if ($option->scala == 0) $vote->text = $request->$value;
        else $vote->value = $request->$value;
    }

    
    private function calculateAnswers($kindPoll){
        switch ($kindPoll){
            case 'Profesor' : return $this->ordenModulos();
            case '--' : return $this->onlyQuestions();
            case 'Actividad' : return $this->searchActivities();
            case 'Fct' : return $this->searchFcts();
        }


    }

    private function ordenModulos(){
        $modulos = collect();
        foreach (AuthUser()->Grupo as $grupo){
            foreach ($grupo->Modulos as $modulo){
                $modulos->push(['modulo'=>$modulo,'profesores'=>$modulo->Profesores()]);
            }
        }
        return $modulos;
    }

    private function searchActivities()
    {
        $actividades = collect();
        foreach (AuthUser()->Grupo as $grupo) {
            foreach ($grupo->Actividades as $actividad) {
                $actividades->push($actividad);
            }
        }
        return $actividades;
    }

    private function searchFcts()
    {
        return Fct::misFcts()->get();
    }
    
}
