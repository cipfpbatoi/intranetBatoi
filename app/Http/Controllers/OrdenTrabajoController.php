<?php
namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Incidencia;
use Intranet\Http\Traits\Imprimir;
use Intranet\Services\StateService;

/**
 * Class OrdenTrabajoController
 * @package Intranet\Http\Controllers
 */
class OrdenTrabajoController extends IntranetController
{

    use Imprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'OrdenTrabajo';
    /**
     * @var array
     */
    protected $gridFields = ['id','Xestado', 'descripcion', 'Xtipo', 'created_at'];
    /**
     * @var string
     */
    protected $descriptionField = 'descripcion';
    /**
     * @var bool
     */
    protected $modal = true;


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.pdf', ['where' => ['estado', '<=', '1']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.resolve', ['where' => ['estado', '==', '1']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.open', ['where' => ['estado', '==', '1']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.anexo',[],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.edit', ['where' => ['estado', '<', '2']],'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('ordentrabajo.delete', ['where' => ['estado', '<', '2']],'mantenimiento'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id){
        Incidencia::where('orden',$id)->get();
        return parent::destroy($id);
    }

    /**
     * @param $id
     * @param string $orientacion
     * @return mixed
     */

    //TODO

    public function imprime($id, $orientacion = 'portrait')
    {
        $elemento = $this->class::findOrFail($id);
        $incidencias = Incidencia::where('orden',$elemento->id)->get();
        $informe = 'pdf.' . strtolower($this->model);
        $pdf = self::hazPdf($informe, $incidencias,$elemento, $orientacion);
        if ($elemento->estado == 0){
            $elemento->estado = 1;
            $elemento->save();
        }
        return $pdf->stream();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolve($id){
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 2;
        $incidencias = Incidencia::where('orden',$elemento->id)->get();
        foreach ($incidencias as $incidencia){
            $staSer = new StateService($incidencia);
            $staSer->resolve();
        }
        $elemento->save();
        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function open($id){
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 0;
        $elemento->save();
        return back();
    }
            
    
    

}
