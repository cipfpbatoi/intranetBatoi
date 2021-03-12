<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Ciclo;
use Intranet\Http\Requests\CicloRequest;

/**
 * Class CicloController
 * @package Intranet\Http\Controllers
 */
class CicloController extends ModalController
{

    /**
     * @var string
     */
    protected $model = 'Ciclo';
    /**
     * @var array
     */
    protected $gridFields = [ 'ciclo','literal','Xdepartamento','Xtipo'];


    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('ciclo.create', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.show', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.edit', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.delete', ['roles' => config('roles.rol.administrador')]));
    }

    public function store(CicloRequest $request)
    {
        $newArt = new Ciclo();
        $newArt->fillAll($request);
        return $this->redirect();
    }

    public function update(CicloRequest $request, $id)
    {
        Ciclo::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

}
