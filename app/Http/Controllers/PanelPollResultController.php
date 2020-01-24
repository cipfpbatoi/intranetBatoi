<?php

namespace Intranet\Http\Controllers;

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
        if (esRol(AuthUser()->rol, config('roles.rol.practicas'))) {
            $practiques = $polls->where('modelo', '==', 'Intranet\Entities\Poll\AlumnoFct');
            $practiques = $practiques->union($polls->where('modelo', '==', 'Intranet\Entities\Poll\Fct'));
        }
        else
            $practiques = [];
        $professorat = $polls->where('state','Finalizada')->where('modelo','Intranet\Entities\Poll\Profesor');
        return $professorat->union($practiques);
    }


}
