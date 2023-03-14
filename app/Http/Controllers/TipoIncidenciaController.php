<?php

namespace Intranet\Http\Controllers;


use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\TipoIncidencia;
use Intranet\Http\Requests\TipoIncidenciaRequest;


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
    protected $gridFields = ['id', 'nombre', 'nom','profesor','tipo'];
    protected $formFields = [
        'id' => ['type' => 'text'],
        'nombre' => ['type' => 'text'],
        'nom' => ['type' => 'text'],
        'idProfesor' => ['type' => 'select'],
        'tipus' => ['type' => 'select']
    ];
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
        $new = new TipoIncidencia();
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(TipoIncidenciaRequest $request, $id)
    {
        TipoIncidencia::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

}
