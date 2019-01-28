<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Poll\Option;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;

class OptionController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    protected $perfil = 'profesor';
    protected $model = 'Option';
    protected $gridFields = [ 'question','scala'];
    
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('option.edit'));
        $this->panel->setBoton('grid', new BotonImg('option.delete'));
    }
    
    
    
}
