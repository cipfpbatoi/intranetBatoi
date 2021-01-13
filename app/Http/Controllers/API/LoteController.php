<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Lote;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Jenssegers\Date\Date;

class LoteController extends ApiBaseController
{

    protected $model = 'Lote';

    public function destroy($id)
    {
        $lote = Lote::find($id);
        $lote_esborrats = Lote::find(1);
        foreach ($lote->Articulos as $articulo){
            $articulo->lote_id = 1;
            if (!isset($articulo->descripcion)){
                $articulo->descripcion = $lote->descripcion;
            }
            $articulo->save();
            $lote_esborrats->unidades += $articulo->unidades;
        }
        $lote->delete();
        $lote_esborrats->save();
        return $this->sendResponse(['deleted' => true,'total'=>$lote_esborrats->unidades], 'OK');
    }

    function getArticulos($lote){
        $lote = Lote::find($lote);
        return response()->json(['data' => $lote->Articulos,'lote'=> $lote]);
    }

    function putArticulos(Request $request,$lote){
        $lote = Lote::find($lote);
        $loteACopiar = Lote::find($request->lote);
        if ($lote->procedencia !== $loteACopiar->procedencia) {
            return $this->sendError('Procedencia diferent');
        }
        foreach ($lote->Articulos as $articulo){
            $articulo->lote_id = $request->lote;
            if (!isset($articulo->descripcion)){
                $articulo->descripcion = $lote->descripcion;
            }
            $loteACopiar->unidades += $articulo->unidades;
            $articulo->save();
            $lote->delete();
            $loteACopiar->save();
        }
        return $this->sendResponse(['updated' => json_encode($request),'total'=>$loteACopiar->unidades], 'OK');
    }





}
