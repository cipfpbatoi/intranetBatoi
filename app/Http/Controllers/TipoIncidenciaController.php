<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\TipoIncidencia;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\TipoIncidenciaRequest;
use Intranet\Presentation\Crud\TipoIncidenciaCrudSchema;


/**
 * Class TipoIncidenciaController
 * @package Intranet\Http\Controllers
 */
class TipoIncidenciaController extends ModalController
{
    const ADMINISTRADOR = 'roles.rol.administrador';

    /**
     * @var array
     */
    protected $gridFields = TipoIncidenciaCrudSchema::GRID_FIELDS;
    protected $formFields = TipoIncidenciaCrudSchema::FORM_FIELDS;
    /**
     * @var string
     */
    protected $model = 'TipoIncidencia';

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return TipoIncidencia
     */
    private function findTipoOrFail($id): TipoIncidencia
    {
        try {
            return TipoIncidencia::findOrFail((int) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Tipus d\'incidència no trobat', ['tipo_incidencia_id' => $id]);
        }
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton(
            'index',
            new BotonBasico('tipoincidencia.create', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('tipoincidencia.show', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('tipoincidencia.edit', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('tipoincidencia.delete', ['roles' => config(self::ADMINISTRADOR)]));
    }

    protected function search()
    {
        return $this->class::all();
    }

    public function store(TipoIncidenciaRequest $request)
    {
        $this->authorize('create', TipoIncidencia::class);
        $this->persist($request);
        return $this->redirect();
    }

    /**
     * @param TipoIncidenciaRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(TipoIncidenciaRequest $request, $id)
    {
        $this->authorize('update', $this->findTipoOrFail($id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un tipus d'incidència amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->findTipoOrFail($id));
        return parent::destroy($id);
    }

}
