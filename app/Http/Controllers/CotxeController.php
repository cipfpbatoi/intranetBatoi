<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Entities\Cotxe;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\CotxeRequest;
use Intranet\Presentation\Crud\CotxeCrudSchema;

/**
 * Controlador de gestió de cotxes.
 */
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

    /**
     * @param CotxeRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(CotxeRequest $request, $id)
    {
        $cotxe = $this->findModelOrFail(Cotxe::class, $id, 'Cotxe no trobat', ['cotxe_id' => $id]);
        $this->authorize('update', $cotxe);
        $request->merge(['idProfesor' => authUser()->dni]);
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un cotxe amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $cotxe = $this->findModelOrFail(Cotxe::class, $id, 'Cotxe no trobat', ['cotxe_id' => $id]);
        $this->authorize('delete', $cotxe);
        return parent::destroy($id);
    }
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['edit','delete']);
    }

}
