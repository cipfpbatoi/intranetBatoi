<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Http\Requests\TipoActividadRequest;
use Intranet\Http\Requests\TipoActividadUpdateRequest;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\TipoActividad;
use Intranet\Presentation\Crud\TipoActividadCrudSchema;


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

    protected $formFields = TipoActividadCrudSchema::FORM_FIELDS;



    protected $gridFields = TipoActividadCrudSchema::GRID_FIELDS;


    public function store(TipoActividadRequest $request)
    {
        $this->authorize('create', TipoActividad::class);

        if (esRol(authUser()->rol,config('roles.rol.jefe_dpto'))) {
            $request->merge(['departamento_id' => authUser()->departamento]);
        }
        $this->persist($request);

        return $this->redirect();
    }

    public function update(TipoActividadUpdateRequest $request, $id)
    {
        $this->authorize('update', TipoActividad::findOrFail((int) $id));
        $request->merge(['departamento_id' => authUser()->departamento]);
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un tipus d'activitat amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', TipoActividad::findOrFail((int) $id));
        return parent::destroy($id);
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
