<?php

namespace Intranet\Http\Controllers;

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
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Hash;

class PPollController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades

    protected $model = 'PPoll';
    protected $gridFields = [ 'id','title','quien','que'];
    protected $vista = [ 'show' => 'poll.masterslave'];
    protected $modal = true;
    
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("ppoll.create",inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('ppoll.edit',inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('ppoll.delete',inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('ppoll.slave',array_merge(['img'=>'fa-plus'],inRol('qualitat'))));
    }
}
