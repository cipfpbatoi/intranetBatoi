<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Entities\Tutoria;
use Intranet\Presentation\Crud\TutoriaGrupoCrudSchema;
use Intranet\UI\Panels\Panel;

class TutoriaGrupoController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'TutoriaGrupo';
    protected $gridFields = TutoriaGrupoCrudSchema::GRID_FIELDS;
    protected $redirect = 'TutoriaController@index';
    protected $modal = true;
    
    public function createfrom($tutoria,$grupo)
    {
        return parent::create(['idTutoria' => $tutoria,'idGrupo'=>$grupo]);
    }
    
    public function search()
    {
        $tutoria = Tutoria::query()->find($this->search);
        $this->titulo = ['que' => $tutoria->descripcion ?? ''];
        return TutoriaGrupo::where('idTutoria','=',$this->search)->get();
    }

    public function iniBotones()
    {
        $this->panel->setBotonera([], ['show']);
    }


}
