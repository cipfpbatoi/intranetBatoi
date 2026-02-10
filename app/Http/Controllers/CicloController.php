<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Ciclo;
use Intranet\Http\Requests\CicloRequest;

/**
 * Class CicloController
 * @package Intranet\Http\Controllers
 */
class CicloController extends ModalController
{
    const ADMINISTRADOR = 'roles.rol.administrador';

    /**
     * @var string
     */
    protected $model = 'Ciclo';
    /**
     * @var array
     */
    protected $gridFields = [ 'id','ciclo','literal','Xdepartamento','Xtipo'];


    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('ciclo.create', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.show', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.edit', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.delete', ['roles' => config(self::ADMINISTRADOR)]));
    }

    public function store(CicloRequest $request)
    {
        $new = new Ciclo();
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(CicloRequest $request, $id)
    {
        Ciclo::findOrFail($id)->fillAll($request);
        if ($file = $request->file('competencies')) {
            $file->storeAs(
                'public/Ciclos',
                $id.'.txt'
            );
        }
        return $this->redirect();
    }

}
