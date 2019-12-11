<?php

namespace Intranet\Http\Controllers;

use Response;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Poll\Poll;

class PanelPollResponseController extends PollController
{

    protected function iniBotones()
    {
        $this->panel->setBoton('grid',new BotonImg('poll.do',['img' =>'fa-check-square-o']));
    }

    protected function search()
    {
        $polls = Poll::all();
        $modelo = isset(AuthUser()->nia)?'nia':'dni';
        return $polls->where('state','Activa')->where('keyUser',$modelo);
    }

}
