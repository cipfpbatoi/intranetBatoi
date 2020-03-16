<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Incidencia;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\OrdenTrabajo;

/**
 * Class IncidenciaController
 * @package Intranet\Http\Controllers
 */
class IncidenciaController extends IntranetController
{

    use traitImprimir,traitAutorizar;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Incidencia';
    /**
     * @var array
     */
    protected $gridFields = ['Xestado', 'DesCurta', 'Xespacio', 'XResponsable', 'Xtipo', 'fecha'];
    /**
     * @var string
     */
    protected $descriptionField = 'descripcion';
    /**
     * @var bool
     */
    protected $modal = true;

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function generarOrden($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $orden = OrdenTrabajo::where('tipo',$incidencia->tipo)
                ->where('estado',0)
                ->where('idProfesor', AuthUser()->dni)
                ->get()
                ->first();

        if (!$orden) $this->generateOrder($incidencia);

        $incidencia->orden = $orden->id;
        $incidencia->save();
        if ($incidencia->estado == 1) return $this->accept($id);
        Session::put('pestana',$incidencia->estado);
        return back();
    }

    /**
     * @param $incidencia
     */
    protected function generateOrder(Incidencia $incidencia){
        $orden = new OrdenTrabajo();
        $orden->idProfesor = AuthUser()->dni;
        $orden->estado = 0;
        $orden->tipo = $incidencia->tipo;
        $orden->descripcion = 'Ordre oberta el dia '.Hoy().' pel profesor '.AuthUser()->FullName.' relativa a '.$incidencia->Tipos->literal;
        $orden->save();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeOrden($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $incidencia->orden = null;
        $incidencia->save();
        return back();
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $elemento = Incidencia::findOrFail($id);
        $elemento->setInputType('espacio', ['disabled' => 'disabled']);
        $elemento->setInputType('material', ['disabled' => 'disabled']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        return view('intranet.edit', compact('elemento', 'default', 'modelo'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $id = $this->realStore($request);
        $this->init($id);
        return $this->redirect();
    }

    /*
     *  update (Request,$id) return redirect
     * guarda els valors del formulari
     */
    public function update(Request $request, $id)
    {
        $elemento =  $this->class::findOrFail($id);
        $tipo = $elemento->tipo;
        $this->validateAll($request, $elemento);    // valida les dades
        $elemento->fillAll($request);
        if ($elemento->tipo != $tipo){
            $elemento->responsable =  $elemento->Tipos->idProfesor;
            $explicacion = "T'han assignat una incidència: " . $elemento->descripcion;
            $enlace = "/incidencia/" . $elemento->id . "/edit";
            avisa($elemento->responsable, $explicacion, $enlace);
            $elemento->save();
        }
        return $this->redirect();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function notify($id)
    {
        $elemento = Incidencia::findOrFail($id);
        if ($elemento->responsable) {
            $explicacion = "T'han assignat una incidència: " . $elemento->descripcion;
            $enlace = "/incidencia/" . $id . "/edit";
            avisa($elemento->responsable, $explicacion, $enlace);
        }
        $elemento->estado++;
        $elemento->save();
        return back();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('incidencia.edit', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('incidencia.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('incidencia.notification', ['where' => ['estado', '<', '1']]));

    }


}
