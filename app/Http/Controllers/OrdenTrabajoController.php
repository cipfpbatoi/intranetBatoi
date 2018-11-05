<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Incidencia;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\OrdenTrabajo;

class OrdenTrabajoController extends IntranetController
{

    use traitImprimir;

    protected $perfil = 'profesor';
    protected $model = 'OrdenTrabajo';
    protected $gridFields = ['id','Xestado', 'descripcion', 'Xtipo', 'created_at'];
    protected $descriptionField = 'descripcion';
    protected $modal = true;
    
    
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.pdf', ['where' => ['estado', '<=', '1']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.resolve', ['where' => ['estado', '==', '1']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.open', ['where' => ['estado', '==', '1']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.anexo',[],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.edit', ['where' => ['estado', '<', '2']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.delete', ['where' => ['estado', '<', '2']],'mantenimiento'));
    }
    
    public function destroy($id){
        $todos = Incidencia::where('orden',$id)->get();
        return parent::destroy($id);
    }
    
    public function imprime($id, $orientacion = 'portrait')
    {
        $elemento = $this->class::findOrFail($id);
        $incidencias = Incidencia::where('orden',$elemento->id)->get();
        $informe = 'pdf.' . strtolower($this->model);
        $pdf = $this->hazPdf($informe, $incidencias,$elemento, $orientacion);
        if ($elemento->estado == 0){
            $elemento->estado = 1;
            $elemento->save();
        }
        return $pdf->stream();
    }
    
    public function resolve($id){
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 2;
        $incidencias = Incidencia::where('orden',$elemento->id)->get();
        foreach ($incidencias as $incidencia){
            Incidencia::resolve($incidencia->id);
        }
        $elemento->save();
        return back();
    }
    public function open($id){
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 0;
        $elemento->save();
        return back();
    }
            
    
    

}
