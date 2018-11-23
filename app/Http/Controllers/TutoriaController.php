<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Tutoria;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Entities\Grupo;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;

class TutoriaController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Tutoria';
    protected $gridFields = ['descripcion','tipos','hasta', 'Xobligatoria','Grupo'];

    public function index(){
        Session::forget('redirect');
        if (esRol(AuthUser()->rol, config('roles.rol.orientador'))) return $this->indexTutoria();
        else {
            if ($grupo = Grupo::select('nombre')->QTutor()->get()->first()){
                $this->titulo = ['que' => $grupo->nombre];
                return parent::index();
            }
            else{
                Alert::danger('No eres tutor de cap grup');
                return back();
            }
                
        }
    }
    public function detalle($id)
    {
        return redirect()->route('tutoriagrupo.indice', ['id' => $id]);
    }
    public function indexTutoria()
    {
        $todos = Tutoria::all();
        $this->titulo = ['que' => trans('messages.menu.Orientacion')];
        $this->iniTutBotones();
        return $this->grid($todos, false);
    }

    public function anexo($id)
    {
        $grupo = Grupo::select('codigo')->QTutor()->get()->first()->codigo;
        $elemento = TutoriaGrupo::where('idTutoria', '=', $id)->where('idGrupo', '=', $grupo)->first();
        if (isset($elemento->idGrupo))
            return redirect()->route('tutoriagrupo.edit', ['id' => $elemento->id]);
        else
            return redirect()->route('tutoriagrupo.create',['tutoria' => $id, 'grupo' => $grupo ]);
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
