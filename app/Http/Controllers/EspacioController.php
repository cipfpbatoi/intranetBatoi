<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Espacio;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Illuminate\Support\Facades\Session;

class EspacioController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Espacio';
    protected $gridFields = ['Xdepartamento', 'aula', 'descripcion', 'gMati', 'gVesprada'];
    //protected $modal = true;
    
    public function __construct()
    {
        $this->middleware($this->perfil);
        parent::__construct();
    }

    public function detalle($id)
    {
        return redirect()->route('material.espacio', ['espacio' => $id]);
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('espacio.create', ['roles' => [config('constants.rol.direccion'), config('constants.rol.mantenimiento')]]));
        $this->panel->setBoton('grid', new BotonImg('material.detalle'));
        $this->panel->setBoton('grid', new BotonImg('espacio.edit', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('espacio.delete', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('profile', new BotonIcon('material.detalle', ['icon' => 'fa-folder']));
        $this->panel->setBoton('profile', new BotonIcon('espacio.edit', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('profile', new BotonIcon('espacio.delete', ['roles' => config('constants.rol.direccion'), 'class' => 'btn-danger']));
    }
}
