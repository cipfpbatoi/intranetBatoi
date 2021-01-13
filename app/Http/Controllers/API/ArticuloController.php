<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Articulo;
use Intranet\Entities\Lote;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Jenssegers\Date\Date;

class ArticuloController extends ApiBaseController
{

    protected $model = 'Articulo';


    public function destroy($id)
    {
        $articulo = Articulo::find($id);
        $idLote = $articulo->lote_id;
        $quantitat = $articulo->unidades;
        $articulo->delete();
        $lote = Lote::find($idLote);
        $total = $lote->unidades - $quantitat;
        if ($total == 0){
            $lote->delete();
        } else {
            $lote->unidades = $total;
            $lote->save();
        }
        return $this->sendResponse(['deleted' => true,'total'=>$total,'lote'=>$idLote], 'OK');
    }

    public function putLote($id,$lote_id)
    {
        $articulo = Articulo::find($id);
        $lote_nuevo = Lote::find($lote_id);
        $lote_antiguo = $articulo->Lote;

        $articulo->lote_id = $lote_id;

        $lote_antiguo->unidades -= $articulo->unidades;
        $lote_nuevo->unidades += $articulo->unidades;
        if ($lote_antiguo->unidades == 0){
            $lote_antiguo->delete();
        } else {
            $lote_antiguo->save();
        }
        $lote_nuevo->save();
        $articulo->save();

        return $this->sendResponse(['deleted' => true,
            'total_antiguo'=>$lote_antiguo->unidades,'lote_antiguo'=>$lote_antiguo->id,
            'total_nuevo'=>$lote_nuevo->unidades,'lote_nuevo'=>$lote_nuevo->id], 'OK');

    }

    public function put(Request $request)
    {
        $material = Material::findOrFail($request->id);
        $anterior = $material->unidades;
        $material->unidades = $request->unidades;
        $material->save();
        $material->explicacion = $request->explicacion;
        $material->propiedad = 'unidades';
        $material->anterior = $anterior;
    }
    public function putUnidades(Request $request)
    {

        $material = Material::findOrFail($request->id);
        $anterior = $material->unidades;
        $material->unidades = $request->unidades;
        $material->save();
        $aviso = 'El material '.$material->descripcion. " ubicat a l'espai ".$material->espacio." ha canviat de ".$anterior.' a '.$material->unidades.' unitats: '.$request->explicacion.".";
        avisa(config('contacto.avisos.material'),$aviso,'#','SISTEMA');
        
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

    public function putUbicacion(Request $request)
    {
        $material = Material::findOrFail($request->id);
        $anterior = $material->espacio;
        $material->espacio = $request->ubicacion;
        $material->save();
        $aviso = 'El material '.$material->descripcion. " ubicat a l'espai ".$anterior." ha canviat a ".$material->espacio.': '.$request->explicacion.".";
        avisa(config('contacto.avisos.material'),$aviso,'#','SISTEMA');
        
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

    public function putEstado(Request $request)
    {
        $material = Material::findOrFail($request->id);
        $anterior = $material->getEstadoOptions()[$material->estado];
        $material->estado = $request->estado;
        $material->save();
        $material->estado = $material->getEstadoOptions()[$material->estado];
        $aviso = 'El material '.$material->descripcion. " ubicat a l'espai ".$material->espacio." ha canviat de l'estat ".$anterior.' a '.$material->estado.': '.$request->explicacion.".";
        avisa(config('contacto.avisos.material'),$aviso,'#','SISTEMA');
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

    public function putInventario(Request $request)
    {
        $fecha = new Date();

        $material = Material::findOrFail($request->id);
        if ($request->inventario == 'true') {
            $material->fechaultimoinventario = $fecha->format('Y-m-d');
        } else {
            $material->fechaultimoinventario = "1970-01-01";
        }
        $material->save();
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

}
