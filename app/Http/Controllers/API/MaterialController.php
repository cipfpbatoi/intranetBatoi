<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Material;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Jenssegers\Date\Date;

class MaterialController extends ApiBaseController
{

    protected $model = 'Material';

    function getMaterial($espacio)
    {
        return response()->json(Material::where('espacio', $espacio)->get());
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
