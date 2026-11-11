<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Articulo;
use Intranet\Http\Resources\InventariableResource;
use Throwable;

/**
 * Controlador API d'articles associats a un lot d'inventari.
 */
class ArticuloLoteController extends ApiResourceController
{

    protected $model = 'ArticuloLote';

    /**
     * Crea un article de lot amb una quantitat vàlida.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $unidades = $this->normaliseUnits($request);
            DB::transaction(function () use ($request, $unidades): void {
                $articuloLote = new ArticuloLote();
                $articuloLote->lote_id = $request->lote_id;
                $articuloLote->marca = $request->marca??null;
                $articuloLote->modelo = $request->modelo??null;
                $articuloLote->unidades = $unidades;
                if ($request->articulo_id === 'new'){
                    $articulo = Articulo::create(['descripcion' => $request->descripcion]);
                    $articuloLote->articulo_id = $articulo->id;
                } else {
                    $articuloLote->articulo_id =$request->articulo_id;
                }
                $articuloLote->save();
            });

            return $this->sendResponse(['created' => true], 'OK');
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Les dades no són vàlides.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            report($e);
            Log::error('Error creant un lot de material.', [
                'lote_id' => $request->lote_id,
                'articulo_id' => $request->articulo_id,
                'error' => $e->getMessage(),
            ]);
            return $this->sendError("No s'ha pogut crear l'article del lot.", 500);
        }
    }

    /**
     * Normalitza la quantitat rebuda per l'API.
     *
     * @param Request $request
     * @return int
     * @throws ValidationException
     */
    private function normaliseUnits(Request $request): int
    {
        $validated = $request->validate([
            'unidades' => ['nullable', 'integer', 'min:1'],
        ], [
            'unidades.integer' => 'Les unitats han de ser un nombre enter.',
            'unidades.min' => 'Les unitats han de ser com a mínim una.',
        ]);

        return isset($validated['unidades']) ? (int) $validated['unidades'] : 1;
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
