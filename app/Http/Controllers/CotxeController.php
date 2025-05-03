<?php

namespace Intranet\Http\Controllers;


use Intranet\Entities\Cotxe;
use Intranet\Http\Requests\CotxeRequest;

class CotxeController extends ModalController
{
    /**
     * @var array
     */
    protected $gridFields = ['matricula' ,'marca' ];
    /**
     * @var string
     */
    protected $model = 'Cotxe';



    public function store(CotxeRequest $request)
    {
        $new = new Cotxe();
        $new->idProfesor = authUser()->dni;
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(CotxeRequest $request, $id)
    {
        Cotxe::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['edit','delete']);
    }

}
