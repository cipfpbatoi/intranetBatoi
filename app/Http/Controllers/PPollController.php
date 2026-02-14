<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Poll\Option;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Hash;
use Intranet\Entities\Poll\PPoll;

class PPollController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades

    protected $model = 'PPoll';
    protected $gridFields = [ 'id','title','what'];
    protected $modal = true; 
    
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("ppoll.create",inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('ppoll.edit',inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('ppoll.delete',array_merge(inRol('qualitat'),['where' => ['remains','==','0']])));
        $this->panel->setBoton('grid', new BotonImg('ppoll.show',array_merge(['img'=>'fa-plus'],inRol('qualitat'))));
    }

    public function show($id)
    {
        $elemento = PPoll::findOrFail($id);
        $modelo = $this->model;
        return view('poll.masterslave', compact('elemento','modelo'));
    }
}
