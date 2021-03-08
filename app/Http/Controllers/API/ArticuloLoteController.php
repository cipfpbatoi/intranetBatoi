<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\ArticuloLote;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Http\Requests\ArticuloLoteRequest;

class ArticuloLoteController extends ApiBaseController
{

    protected $model = 'ArticuloLote';


    public function store(Request $request)
    {
        try {
            $this->class::create($request->all());
            return $this->sendResponse(['created' => true], 'OK');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    function getMateriales($articulo){
        $lote = ArticuloLote::find($articulo);
        if (count($lote->Materiales))
            return response()->json(['data' => $lote->Materiales]);
        else response()->json([],404);
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
