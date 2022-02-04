<?php
namespace Intranet\Http\Controllers;

use Response;

trait traitDropZone{

    protected function deleteAttached($id){
        $modelo = strtolower($this->model);
        $path = storage_path()."/app/public/adjuntos/$modelo/$id";
        try{
            $dir = opendir($path);
            while ($elemento = readdir($dir)){
                if( $elemento != "." && $elemento != ".." && !is_dir($path.'/'.$elemento) ){
                    unlink($path . '/' . $elemento);
                }
            }
        }catch (\Exception $e){
            dd($e);
        }
        rmdir($path);
    }

    public function link($id){
        $registre = $this->class::findOrFail($id);
        $modelo = strtolower($this->model);
        $url = back()->getTargetUrl();
        return view('dropzone.index',compact('registre','url','modelo'));
    }

}
