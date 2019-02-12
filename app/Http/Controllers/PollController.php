<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Poll\Option;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;
use Styde\Html\Facades\Alert;

class PollController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    //protected $perfil = 'alumno';
    protected $model = 'Poll';
    protected $gridFields = [ 'id','title','actiu'];
    protected $vista = [ 'show' => 'poll.masterSlave'];
    
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('poll.edit'));
        $this->panel->setBoton('grid', new BotonImg('poll.delete'));
        $this->panel->setBoton('grid', new BotonImg('poll.show'));
        $this->panel->setBoton('grid', new BotonImg('poll.active'));
    }
    
    protected function preparaEnquesta($id){
        $votes = Vote::where('user_id', AuthUser()->nia)
                ->whereIn('option_id', hazArray(Option::where('poll_id',$id)->get(),'id'))
                ->count();
        if ($votes == 0){ 
            $poll = Poll::find($id);
            $modulos = $this->ordenModulos();
            return view('poll.enquesta',compact('modulos','poll'));
        } else {
            Alert::info("Ja has omplit l'enquesta");
            return redirect('home');
        }
    }
    
    protected function guardaEnquesta(Request $request,$id){
        $poll = Poll::find($id);
        $modulos = $this->ordenModulos();
        foreach ($poll->options as $question => $option){
            $profe=0;
            foreach ($modulos as $modulo)
                foreach ($modulo['profesores'] as $profesores)
                    foreach ($profesores as $dni){
                        $profe++;
                        $value = 'option'.($question+1).'_'.$profe;
                        $vote = new Vote();
                        $vote->user_id = AuthUser()->nia;
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
    
    private function ordenModulos(){
        $modulos = collect();
        foreach (AuthUser()->Grupo as $grupo){
            foreach ($grupo->Modulos as $modulo){
                $modulos->push(['modulo'=>$modulo,'profesores'=>$modulo->Profesores()]);
            }
        }
        return $modulos;
    }
    
}
