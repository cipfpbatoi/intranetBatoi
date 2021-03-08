<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Articulo;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Lote;
use Intranet\Entities\Material;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Http\Resources\LoteResource;
use Intranet\Http\Resources\ArticuloLoteResource;
use Jenssegers\Date\Date;

class LoteController extends ApiBaseController
{

    protected $model = 'Lote';

    public function destroy($id)
    {
        $lote = Lote::find($id);
        $lote->delete();
        return $this->sendResponse(['success' => true], 'OK');
    }

    function index(){
        $data = LoteResource::collection(Lote::get());
        return $this->sendResponse($data, 'OK');
    }

    function getArticulos($lote){
        $lote = Lote::find($lote);
        return response()->json(['data' => ArticuloLoteResource::collection($lote->ArticuloLote),'lote'=> $lote->registre]);
    }


    function putArticulos(Request $request,$lote)
    {
        $lote = Lote::find($lote);
        if ($request->inventariar){
            foreach ($lote->ArticuloLote as $articulo){
                for ($i=0;$i<$articulo->unidades;$i++){
                    $material = new Material(
                        [   'descripcion'=>$articulo->descripcion,
                            'marca' => $articulo->marca,
                            'modelo' => $articulo->modelo,
                            'procedencia'=> $lote->procedencia,
                            'estado' => 1,
                            'unidades' => 1,
                            'proveedor' => $lote->proveedor,
                            'inventariable' => 1,
                            'espacio' => 'INVENT',
                            'articulo_lote_id' => $articulo->id
                        ]
                    );
                    $material->save();
                }
            }
        }
        return $this->sendResponse(['data' => $lote ], 'OK');
    }

}
