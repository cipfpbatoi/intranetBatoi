<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Http\Requests\TipoActividadRequest;
use Intranet\Http\Requests\TipoActividadUpdateRequest;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\TipoActividad;


/**
 * Class LoteController
 * @package Intranet\Http\Controllers
 */
class TipoActividadController extends ModalController
{

    /**
     * @var string
     */
    protected $model = 'TipoActividad';

    protected $formFields = [
        'id' => ['type' => 'hidden'],
        'cliteral' => ['type' => 'text'],
        'vliteral' => ['type' => 'text'],
        'justificacio' => ['type' => 'textarea'],
    ];



    protected $gridFields = [ 'id','departamento' ,'vliteral'   ];


    public function store(TipoActividadRequest $request)
    {
        $new = new TipoActividad();

        $new->fillAll($request);
        if (esRol(authUser()->rol,config('roles.rol.jefe_dpto'))) {
            $new->departamento_id = authUser()->departamento;
        }
        $new->save();

        return $this->redirect();
    }

    public function update(TipoActividadUpdateRequest $request, $id)
    {
        $new = TipoActividad::findOrFail($id);
        $new->fillAll($request);
        $new->departamento_id = authUser()->departamento;
        $new->save();
        return $this->redirect();
    }

    public function search()
    {
        if (esRol(authUser()->rol,config('roles.rol.jefe_dpto'))) {
            return TipoActividad::where('departamento_id',authUser()->departamento)->get();
        }
        return TipoActividad::all();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('tipoactividad.create', ['text'=>'Nou tipus Activitat','roles' => [config('roles.rol.direccion'), config('roles.rol.jefe_dpto')]]));
        $this->panel->setBoton('grid',new BotonImg('tipoactividad.edit', [ 'where' => ['fecha_aprobacion', 'isNull','' ],'roles' => [config('roles.rol.direccion'), config('roles.rol.jefe_dpto')]]));
        $this->panel->setBoton('grid',new BotonImg('tipoactividad.delete', [ 'where' => ['fecha_aprobacion', 'isNull','' ],'roles' => [config('roles.rol.direccion'), config('roles.rol.jefe_dpto')]]));
    }


}
