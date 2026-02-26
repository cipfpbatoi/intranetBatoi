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
        $this->authorize('create', Cotxe::class);
        $request->merge(['idProfesor' => authUser()->dni]);
        $this->persist($request);
        return $this->redirect();
    }

    public function update(CotxeRequest $request, $id)
    {
        $this->authorize('update', Cotxe::findOrFail((int) $id));
        $request->merge(['idProfesor' => authUser()->dni]);
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un cotxe amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', Cotxe::findOrFail((int) $id));
        return parent::destroy($id);
    }
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['edit','delete']);
    }

}
