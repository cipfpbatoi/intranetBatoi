<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\PPoll;

class PanelPollResultController extends PollController
{
    protected $gridFields = [ 'id','title'];

    protected function iniBotones()
    {
        $this->panel->setBoton('grid',new BotonImg('poll.show',['img' =>'fa-eye']));
    }

    protected function search()
    {

        if (esRol(AuthUser()->rol, config('roles.rol.practicas'))) {
            $ppolls = hazArray(PPoll::all(),'id','id');
        }
        else {
            $ppolls = hazArray(PPoll::where('what', '==', 'Profesor')->get(),'id','id');
        }
        return Poll::whereIn('idPPoll', $ppolls)
            ->where('hasta', '<=', now())
            ->get();

    }


}
