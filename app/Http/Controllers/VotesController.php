<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Colaboracion;
use Response;

class VotesController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Vote\\'; //string on es troben els models de dades
    protected $model = 'Vote';
    protected $gridFields = ['year', 'question','instructor','answer'];


    protected function showColaboracion($colaboracion){
        return $this->llist(Colaboracion::find($colaboracion)->votes,$this->panel);
    }



}
