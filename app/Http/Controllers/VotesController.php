<?php
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Entities\Colaboracion;

class VotesController extends ModalController
{
    protected $namespace = 'Intranet\\Entities\\Poll\\'; //string on es troben els models de dades
    protected $model = 'VoteAnt';
    protected $gridFields = ['curs', 'question','answer'];


    public function showColaboracion($colaboracion)
    {
        $colaboracion = Colaboracion::find($colaboracion);
        return $this->panel->render($colaboracion->votes, $this->titulo, 'intranet.list');
    }



}
