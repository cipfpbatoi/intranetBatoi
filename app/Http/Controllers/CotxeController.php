<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Entities\Cotxe;
use Intranet\Exceptions\NotFoundDomainException;
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

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return Cotxe
     */
    private function findCotxeOrFail($id): Cotxe
    {
        try {
            return Cotxe::findOrFail((int) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Cotxe no trobat', ['cotxe_id' => $id]);
        }
    }


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
        $this->authorize('update', $this->findCotxeOrFail($id));
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
        $this->authorize('delete', $this->findCotxeOrFail($id));
        return parent::destroy($id);
    }
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['edit','delete']);
    }

}
