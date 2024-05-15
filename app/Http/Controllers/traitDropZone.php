<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Adjunto;
use Intranet\Services\AttachedFileService;
use Response;

trait traitDropZone
{
    protected function deleteAttached($id)
    {
        $path = strtolower($this->model)."/$id";
        $attached = Adjunto::getByPath($path)->get();
        foreach ($attached as $attach) {
            AttachedFileService::delete($attach);
            $directory = $attach->directory;
        }
        if (isset($directory)) {
            rmdir($directory);
        }
    }

    public function link($id)
    {
        $registre = $this->class::findOrFail($id);
        $quien = $registre->quien;
        $modelo = strtolower($this->model);

        $botones = [
            'volver' => ['link' => back()->getTargetUrl()]
        ];

        return view('dropzone.index', compact('modelo', 'id', 'quien', 'botones'));
    }

}


