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
    protected $gridFields = [ 'id','title','actiu'];
    
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('poll.edit'));
        $this->panel->setBoton('grid', new BotonImg('poll.delete'));
        $this->panel->setBoton('grid', new BotonImg('poll.show'));
        $this->panel->setBoton('grid', new BotonImg('poll.active'));
    }
    
    public function show($id)
    {
        $poll = Poll::find($id);
        $options = $poll->option
        return view('poll.show',compact('polls'));
    }
    
}
