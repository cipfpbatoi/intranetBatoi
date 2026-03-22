<?php
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Material;
use Intranet\Entities\Incidencia;
use Intranet\Entities\TipoIncidencia;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Presentation\Crud\MaterialCrudSchema;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class MaterialController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Material';
    /**
     * @var array
     */
    protected $vista = ['index' => 'Material'];
    /**
     * @var array
     */
    protected $gridFields = MaterialCrudSchema::GRID_FIELDS;
    protected $formFields = MaterialCrudSchema::FORM_FIELDS;
    /**
     * @var array
     */

    /**
     * MaterialController constructor.
     */
    public function __construct()
    {
        $this->middleware($this->perfil);
        parent::__construct();
    }

    /**
     *
     */
    public function iniBotones()
    {
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                'material.create',
                ['roles' => [config('roles.rol.direccion'), config('roles.rol.mantenimiento')]]
            )
        );
    }



    /**
     * @param $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function copy($id)
    {
        try {
            $elemento = Material::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Material no trobat', ['material_id' => $id], $e);
        }
        $copia = new Material;
        $copia->fill($elemento->toArray());
        $copia->save();
        return redirect()->route('material.edit', ['material' => $copia->id]);
    }

    /**
     * @param $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function incidencia($id)
    {
        try {
            $elemento = Material::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Material no trobat', ['material_id' => $id], $e);
        }

        try {
            $tipo = TipoIncidencia::where('tipus', 1)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Tipus d\'incidència no trobat', ['tipus' => 1], $e);
        }
        $incidencia = new Incidencia(['tipo'=> $tipo->id,
            'material' => $id,
            'estado' => 0,
            'espacio' => $elemento->espacio,
            'descripcion' => $elemento->descripcion,
            'idProfesor' => AuthUser()->dni,
            'fecha' => Hoy()]);
        $incidencia->save();
        Incidencia::putEstado($incidencia->id, 1);
        return redirect()->route('incidencia.edit', ['incidencium' => $incidencia->id]);
    }



}
