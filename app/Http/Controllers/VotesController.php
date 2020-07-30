<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Colaboracion;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\PPoll;
use Response;

class VotesController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Vote\\'; //string on es troben els models de dades
    protected $model = 'VoteAnt';
    protected $gridFields = ['curs', 'question','answer'];


    protected function showColaboracion($colaboracion){
        $ppol = hazArray(PPoll::where('what','Fct')->get(),'id','id');
        $poll = hazArray(Poll::whereIn('idPPoll',$ppol)->get(),'id','id');
        $colaboracion = Colaboracion::find($colaboracion);
        return $this->llist($colaboracion->votes,$this->panel);
    }



}
