<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Incidencia;
use Intranet\Botones\BotonImg;
use Intranet\Http\Requests\IncidenciaRequest;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\OrdenTrabajo;
use Intranet\Services\AdviseService;
use Intranet\Services\FormBuilder;


/**
 * Class IncidenciaController
 * @package Intranet\Http\Controllers
 */
class IncidenciaController extends ModalController
{

    use traitImprimir,traitAutorizar;

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
    protected $formFields = [
        'tipo' => ['type' => 'select'],
        'espacio' => ['type' => 'select'],
        'material' => ['type' => 'select'],
        'descripcion' => ['type' => 'textarea'],
        'idProfesor' => ['type' => 'hidden'],
        'prioridad' => ['type' => 'select'],
        'observaciones' => ['type' => 'text'],
        'fecha' => ['type' => 'date']
    ];


    protected function search(){
        return Incidencia::with('Tipos')
            ->with('Responsables')
            ->with('Creador')
            ->where('idProfesor', '=', AuthUser()->dni)
            ->get();
    }
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

        if (!$orden) {
            $orden = $this->generateOrder($incidencia);
        }

        $incidencia->orden = $orden->id;
        $incidencia->save();
        if ($incidencia->estado == 1) {
            return $this->accept($id);
        }
        Session::put('pestana',$incidencia->estado);
        return back();
    }

    /**
     * @param $incidencia
     */
    protected function generateOrder(Incidencia $incidencia):OrdenTrabajo{
        $orden = new OrdenTrabajo();
        $orden->idProfesor = AuthUser()->dni;
        $orden->estado = 0;
        $orden->tipo = $incidencia->tipo;
        $orden->descripcion = 'Ordre oberta el dia '.Hoy().' pel profesor '.AuthUser()->FullName.' relativa a '.$incidencia->Tipos->literal;
        $orden->save();
        return $orden;
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
        $formulario = new FormBuilder($elemento,[
            'espacio' => ['disabled' => 'disabled'],
            'material' => ['disabled' => 'disabled'],
            'descripcion' => ['type' => 'textarea'],
            'idProfesor' => ['type' => 'hidden'],
            'tipo' => ['type' => 'select'],
            'prioridad' => ['type' => 'select'],
            'observaciones' => ['type' => 'text'],
            'fecha' => ['type' => 'date']
        ]);
        $modelo = $this->model;
        return view('intranet.edit', compact('formulario',  'modelo'));
    }


    public function store(IncidenciaRequest $request)
    {
        $new = new Incidencia();
        $new->fillAll($request);
        Incidencia::putEstado($new->id,$this->init);
        return $this->redirect();
    }


    /*
     *  update (Request,$id) return redirect
     * guarda els valors del formulari
     */
    public function update(IncidenciaRequest $request, $id)
    {
        $elemento =  Incidencia::findOrFail($id);
        $tipo = $elemento->tipo;
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

    protected function createWithDefaultValues($default = [])
    {
        return new Incidencia(['idProfesor'=>AuthUser()->dni,'fecha'=>Hoy('Y-m-d')]);
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
