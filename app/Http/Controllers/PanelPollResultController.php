<?php

namespace Intranet\Http\Controllers;

use Response;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Poll\Poll;

class PanelPollResultController extends PollController
{
    protected $gridFields = [ 'id','title'];

    protected function iniBotones()
    {
        $this->panel->setBoton('grid',new BotonImg('poll.show',['img' =>'fa-eye']));
    }

    protected function search()
    {
        $polls = Poll::all();
        if (esRol(AuthUser()->rol, config('roles.rol.practicas')))
            return $polls->where('state','Finalizada');
        else
            return $polls->where('state','Finalizada')->where('modelo','Intranet\Entities\Poll\Profesor');
    }


}
