<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Fct;
use Response;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Poll\Poll;

class PanelPollResponseController extends PollController
{

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('poll.do', ['img' =>'fa-check-square-o']));
    }

    protected function search()
    {
        $polls = Poll::all();
        $key = isset(AuthUser()->nia)?'nia':'dni';
        $activas =  $polls->where('state', 'Activa')->where('keyUser', $key);
        $usuario = [];
        foreach ($activas as $activa) {
            $modelo = $activa->modelo;
            if ($modelo::has()) {
                $usuario[] = $activa;
            }
        }
        return $usuario;
    }

}
