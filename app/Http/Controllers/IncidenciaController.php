<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Incidencia;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\OrdenTrabajo;

class IncidenciaController extends IntranetController
{

    use traitImprimir,traitAutorizar;

    protected $perfil = 'profesor';
    protected $model = 'Incidencia';
    protected $gridFields = ['Xestado', 'DesCurta', 'espacio', 'XResponsable', 'Xtipo', 'fecha'];
    protected $descriptionField = 'descripcion';
    protected $modal = true;
   
    
    
    protected function orden($id)
    {
        $elemento = Incidencia::findOrFail($id);
        $orden = OrdenTrabajo::where('tipo',$elemento->tipo)
                ->where('estado',0)
                ->where('idProfesor', AuthUser()->dni)
                ->get()
                ->first();
        if (!$orden){
            $orden = new OrdenTrabajo();
            $orden->idProfesor = AuthUser()->dni;
            $orden->estado = 0;
            $orden->tipo = $elemento->tipo;
            $orden->descripcion = 'Ordre oberta el dia '.Hoy().' pel profesor '.AuthUser()->FullName.' relativa a '.$elemento->Tipos->literal;
            $orden->save();
        }
        $elemento->orden = $orden->id;
        $elemento->save();
        if ($elemento->estado == 1) return $this->accept ($id);
        Session::put('pestana',$elemento->estado);
        return back();
    }

    public function anexo($id)
    {
        $todos = Incidencia::where('orden',$id)->get(); 
        $this->panel->setPestana(trans('validation.attributes.orden').' '.$id, true, 'profile.incidencia',null,null,1);
        $this->panel->setBoton('index', new BotonBasico("$id.pdf", ['where'=>['estado','==',0]],"mantenimiento/ordentrabajo" ));
        $this->panel->setBoton('index', new BotonBasico("ordentrabajo.", ['text'=>trans('messages.buttons.verorden')],"mantenimiento" ));
        $this->panel->setBoton('profile', new BotonIcon("incidencia.remove", ['class' => 'btn-danger unauthorize','where'=>['estado','<',3]],'mantenimiento'));
        
        return $this->grid($todos, false);
    }
    
    public function remove($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $incidencia->orden = null;
        $incidencia->save();
        return back();
    }

    public function edit($id)
    {
        $elemento = Incidencia::findOrFail($id);
        $elemento->setInputType('espacio', ['disabled' => 'disabled']);
        $elemento->setInputType('material', ['disabled' => 'disabled']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        return view('intranet.edit', compact('elemento', 'default', 'modelo'));
    }
    
    public function store(Request $request)
    {
        $id = $this->realStore($request);
        $this->init($id);
        return $this->redirect();
    }

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

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('incidencia.edit', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('incidencia.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('incidencia.notification', ['where' => ['estado', '<', '1']]));
        
    }

}
