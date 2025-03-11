<?php

namespace Intranet\Http\Traits;

use Intranet\Entities\Adjunto;
use Intranet\Services\AttachedFileService;


trait DropZone
{
    protected function deleteAttached($id)
    {
        $path = strtolower($this->model) . "/$id";
        $attachedFiles = Adjunto::getByPath($path)->get();

        foreach ($attachedFiles as $attach) {
            AttachedFileService::delete($attach);
        }
    }

    public function link(int $id)
    {
        if (!isset($this->model)) {
            abort(500, "El atributo 'model' no está definido en la clase que usa traitDropZone.");
        }

        $registre = $this->class::findOrFail($id);
        $quien = $registre->quien;
        $modelo = strtolower($this->model);

        $botones = [
            'volver' => ['link' => url()->previous()]
        ];

        return view('dropzone.index', compact('modelo', 'id', 'quien', 'botones'));
    }
}

