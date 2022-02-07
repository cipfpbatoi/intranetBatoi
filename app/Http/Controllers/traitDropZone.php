<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Adjunto;
use Intranet\Services\AttachedFileService;
use Response;

trait traitDropZone{

    protected function deleteAttached($id){
        $modelo = strtolower($this->model);
        $attached = Adjunto::findByModel($modelo,$id)->get();
        foreach ($attached as $attach){
            AttachedFileService::delete($attach);
            $directory = $attach->directory;
        }
        rmdir($directory);
    }

    public function link($id){
        $registre = $this->class::findOrFail($id);
        $quien = $registre->quien;
        $modelo = strtolower($this->model);
        $back = back()->getTargetUrl();
        return view('dropzone.index',compact('modelo','id','quien','back'));
    }

}
