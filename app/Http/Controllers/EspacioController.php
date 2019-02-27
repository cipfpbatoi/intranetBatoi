<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Espacio;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Illuminate\Support\Facades\Session;

/**
 * Class EspacioController
 * @package Intranet\Http\Controllers
 */
class EspacioController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Espacio';
    /**
     * @var array
     */
    protected $gridFields = ['Xdepartamento', 'aula', 'descripcion', 'gMati', 'gVesprada'];
    /**
     * @var bool
     */
    protected $modal = true;

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
        $this->panel->setBoton('index', new BotonBasico('espacio.create', ['roles' => [config('roles.rol.direccion'), config('roles.rol.mantenimiento')]]));
        $this->panel->setBoton('grid', new BotonImg('material.detalle'));
        $this->panel->setBoton('grid', new BotonImg('espacio.edit', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('espacio.delete', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBoton('profile', new BotonIcon('material.detalle', ['icon' => 'fa-folder']));
        $this->panel->setBoton('profile', new BotonIcon('espacio.edit', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBoton('profile', new BotonIcon('espacio.delete', ['roles' => config('roles.rol.direccion'), 'class' => 'btn-danger']));
    }
}
