<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Espacio;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Http\Requests\EspacioRequest;

/**
 * Class EspacioController
 * @package Intranet\Http\Controllers
 */
class EspacioController extends ModalController
{

    /**
     * @var string
     */
    protected $model = 'Espacio';
    /**
     * @var array
     */
    protected $gridFields = ['Xdepartamento', 'aula', 'descripcion', 'gMati', 'gVesprada'];


    public function store(EspacioRequest $request)
    {
        $new = new Espacio();
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(EspacioRequest $request, $id)
    {
        Espacio::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detalle($id)
    {
        return redirect()->route('material.espacio', ['espacio' => $id]);
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('espacio.create', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('material.detalle'));
        $this->panel->setBoton('grid', new BotonImg('espacio.edit', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('espacio.delete', ['roles' => config('roles.rol.direccion')]));
    }
}
