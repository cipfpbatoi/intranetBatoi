<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Poll\Poll;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;

class PollController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    protected $perfil = 'profesor';
    protected $model = 'Poll';
    protected $gridFields = [ 'id','question','isClosed'];
    
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('poll.edit'));
        $this->panel->setBoton('grid', new BotonImg('poll.delete'));
        $this->panel->setBoton('grid', new BotonImg('poll.show'));
    }
    
    public function show($id)
    {
        $polls = Poll::all();
        return view('poll.show',compact('polls'));
    }
}
