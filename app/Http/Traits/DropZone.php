<?php

namespace Intranet\Http\Traits;

use Intranet\Entities\Adjunto;
use Intranet\Services\Document\AttachedFileService;
use Illuminate\Support\Facades\Log;


/**
 * Trait per gestionar la vista DropZone i la neteja d'adjunts associats.
 *
 * Contracte esperat del controller que usa el trait:
 * - `protected string $model`: nom curt del model (ex. `Solicitud`).
 * - `protected string $class`: FQCN de l'entitat (ex. `Intranet\Entities\Solicitud`).
 */
trait DropZone
{
    /**
     * Elimina tots els adjunts vinculats al path `{model}/{id}`.
     *
     * @param int|string $id Identificador del registre.
     */
    protected function deleteAttached($id)
    {
        $path = strtolower($this->model) . "/$id";
        $attachedFiles = Adjunto::getByPath($path)->get();

        foreach ($attachedFiles as $attach) {
            try {
                AttachedFileService::delete($attach);
            } catch (\Throwable $exception) {
                Log::warning('DropZone: no s\'ha pogut eliminar un adjunt.', [
                    'id' => $attach->id ?? null,
                    'route' => $attach->route ?? null,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * Mostra la pantalla d'adjunts DropZone per a un registre.
     *
     * @param int $id Identificador del registre.
     * @return \Illuminate\Contracts\View\View
     */
    public function link(int $id)
    {
        if (!isset($this->model)) {
            abort(500, "L'atribut 'model' no està definit en la classe que usa el trait DropZone.");
        }

        if (!isset($this->class)) {
            abort(500, "L'atribut 'class' no està definit en la classe que usa el trait DropZone.");
        }

        $registre = $this->class::findOrFail($id);
        $quien = $registre->quien ?? $registre->fullName ?? $registre->nombre ?? "#$id";
        $modelo = strtolower($this->model);

        $botones = [
            'volver' => ['link' => url()->previous()]
        ];

        return view('dropzone.index', compact('modelo', 'id', 'quien', 'botones'));
    }
}
