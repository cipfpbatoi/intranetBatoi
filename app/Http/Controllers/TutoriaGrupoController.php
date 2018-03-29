<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Entities\Tutoria;
use Intranet\Botones\Panel;

class TutoriaGrupoController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'TutoriaGrupo';
    protected $gridFields = ['idGrupo', 'observaciones', 'fecha'];
    protected $redirect = 'TutoriaController@index';
    
    public function createfrom($tutoria,$grupo)
    {
        return parent::create(['idTutoria' => $tutoria,'idGrupo'=>$grupo]);
    }
    
    public function indice($id)
    {
        $desc = Tutoria::find($id)->descripcion;
        $todos = TutoriaGrupo::where('idTutoria','=',$id)->get();
        foreach ($todos as $uno){
            $uno->idGrupo = $uno->Grupo->nombre;
        }
        $this->titulo = ['que' => $desc];
        return $this->llist($todos, new Panel('TutoriaGrupo', ['idGrupo', 'observaciones', 'fecha'], 'grid.standard'));
    }
    
}
