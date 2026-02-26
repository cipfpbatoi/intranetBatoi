<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;


use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\TipoIncidencia;
use Intranet\Http\Requests\TipoIncidenciaRequest;
use Intranet\Presentation\Crud\TipoIncidenciaCrudSchema;


/**
 * Class ComisionController
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

    public function update(TipoIncidenciaRequest $request, $id)
    {
        $this->authorize('update', TipoIncidencia::findOrFail((int) $id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un tipus d'incidència amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', TipoIncidencia::findOrFail((int) $id));
        return parent::destroy($id);
    }

}
