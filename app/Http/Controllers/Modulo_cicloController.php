<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Http\Requests\ModuloCicloRequest;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Modulo_ciclo;

/**
 * Class Modulo_cicloController
 * @package Intranet\Http\Controllers
 */
class Modulo_cicloController extends ModalController
{
    const ROLES_ROL_ADMINISTRADOR = 'roles.rol.administrador';

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Modulo_ciclo';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'Xmodulo','Xciclo','curso','enlace','Xdepartamento'];
    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('modulo_ciclo.create', ['roles' => config(self::ROLES_ROL_ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.edit', ['roles' => config(self::ROLES_ROL_ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.delete', ['roles' => config(self::ROLES_ROL_ADMINISTRADOR)]));
    }

    public function store(ModuloCicloRequest $request)
    {
        $this->authorize('create', Modulo_ciclo::class);
        $this->persist($request);
        return $this->redirect();
    }

    public function update(ModuloCicloRequest $request, $id)
    {
        $this->authorize('update', Modulo_ciclo::findOrFail((int) $id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un enllaç mòdul-cicle amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', Modulo_ciclo::findOrFail((int) $id));
        return parent::destroy($id);
    }

}
