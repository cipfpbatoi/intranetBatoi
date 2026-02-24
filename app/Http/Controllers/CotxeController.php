<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;


use Intranet\Entities\Cotxe;
use Intranet\Http\Requests\CotxeRequest;
use Intranet\Presentation\Crud\CotxeCrudSchema;

class CotxeController extends ModalController
{
    /**
     * @var array
     */
    protected $gridFields = CotxeCrudSchema::GRID_FIELDS;
    protected $formFields = CotxeCrudSchema::FORM_FIELDS;
    /**
     * @var string
     */
    protected $model = 'Cotxe';



    public function store(CotxeRequest $request)
    {
        $request->merge(['idProfesor' => authUser()->dni]);
        $this->persist($request);
        return $this->redirect();
    }

    public function update(CotxeRequest $request, $id)
    {
        $this->persist($request, $id);
        return $this->redirect();
    }
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['edit','delete']);
    }

}
