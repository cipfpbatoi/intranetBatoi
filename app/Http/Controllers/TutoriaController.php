<?php

namespace Intranet\Http\Controllers;

use Response;
use Intranet\Entities\Tutoria;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Entities\Grupo;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;

class TutoriaController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Tutoria';
    protected $gridFields = ['descripcion','tipos','hasta', 'Xobligatoria','Grupo','feedBack'];

    public function index()
    {
        Session::forget('redirect');
        if (esRol(AuthUser()->rol, config('roles.rol.orientador'))) {
            return $this->indexTutoria();
        }

        if ($grupo = Grupo::select('nombre')->QTutor()->get()->first()) {
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
        $this->titulo = ['que' => trans('messages.menu.Orientacion')];
        $this->iniTutBotones();
        return $this->grid($todos, false);
    }

    public function anexo($id)
    {
        $grupo = Grupo::select('codigo')->QTutor()->get()->first()->codigo;
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
