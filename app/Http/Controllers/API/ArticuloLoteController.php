<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Articulo;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Http\Resources\InventariableResource;

class ArticuloLoteController extends ApiBaseController
{

    protected $model = 'ArticuloLote';


    public function store(Request $request)
    {
        try {
            $articuloLote = new ArticuloLote();
            $articuloLote->lote_id = $request->lote_id;
            $articuloLote->marca = $request->marca??null;
            $articuloLote->modelo = $request->modelo??null;
            $articuloLote->unidades = $request->unidades;
            if ($request->articulo_id === 'new'){
                $articulo = Articulo::create(['descripcion' => $request->descripcion]);
                $articuloLote->articulo_id = $articulo->id;
            } else {
                $articuloLote->articulo_id =$request->articulo_id;
            }
            $articuloLote->save();
            return $this->sendResponse(['created' => true], 'OK');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    function getMateriales($articulo){
        $lote = ArticuloLote::find($articulo);
        if (count($lote->Materiales)) {
            //return response()->json(['data' => $lote->Materiales]);
            return response()->json(['data' => InventariableResource::collection($lote->Materiales)]);

        }
        else {
            response()->json([], 404);
        }
    }
    /**
    function postArticulo(ArticuloLoteRequest $request){
        $loteArticulo = new ArticuloLote(
            [   'lote_id' => $request->lote_id,
                'articulo_id' => $request->articulo_id,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'unidades' => $request->unidades]
        );
        $loteArticulo->save();
        return $this->sendResponse(['data' => $lote ], 'OK');
    }
    */
}
