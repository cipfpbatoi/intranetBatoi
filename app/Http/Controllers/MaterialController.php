<?php
namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Entities\Material;
use Intranet\Entities\Incidencia;
use Intranet\Entities\TipoIncidencia;

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
    protected $gridFields = ['id', 'descripcion', 'Estado', 'espacio', 'unidades'];
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
        $this->panel->setBoton('index', new BotonBasico('material.create', ['roles' => [config('roles.rol.direccion'), config('roles.rol.mantenimiento')]]));
    }



    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function copy($id)
    {
        $elemento = Material::find($id);
        $copia = new Material;
        $copia->fill($elemento->toArray());
        $copia->save();
        return redirect("/material/$copia->id/edit");
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function incidencia($id)
    {
        $tipo = TipoIncidencia::where('tipus',1)->first();
        $elemento = Material::find($id);
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
