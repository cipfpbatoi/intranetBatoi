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
    protected $gridFields = ['Nombre', 'observaciones', 'fecha'];
    protected $redirect = 'TutoriaController@index';
    
    public function createfrom($tutoria,$grupo)
    {
        return parent::create(['idTutoria' => $tutoria,'idGrupo'=>$grupo]);
    }
    
    public function search(){
        $this->titulo = ['que' => Tutoria::find($id)->descripcion];
        return TutoriaGrupo::where('idTutoria','=',$this->search)->get();
    }
    
}
