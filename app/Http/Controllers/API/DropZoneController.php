<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;

class DropZoneController extends ApiBaseController
{
    public function getAttached($modelo,$id){
        $path = storage_path()."/app/public/adjuntos/$modelo/$id";
        $data = [];
        try{
            $dir = opendir($path);
        } catch (\Exception $e){
            return $this->sendResponse($data,'OK');
        }

        $i= 0;
        while ($elemento = readdir($dir)){
            if( $elemento != "." && $elemento != ".." && !is_dir($path.$elemento) ){
                $i++;
                $data[$i]['name'] = $elemento;
                try{
                    $data[$i]['size'] = filesize($path.$elemento);
                } catch (\Exception $e){
                    $data[$i]['size'] = 9999;
                }
                $data[$i]['accepted'] = true;
            }
        }
        return $this->sendResponse($data, 'OK');
    }

    public function removeAttached($modelo,$id,$file){
        $path = storage_path()."/app/public/adjuntos/$modelo/$id/$file";
        if (is_file($path)) {
            unlink($path);
        }
        return $this->sendResponse([],'OK');
    }

    public function attachFile(Request $request){
        $id = $request->id;
        $modelo = $request->modelo;
        $path = storage_path()."/app/public/adjuntos/$modelo/$id";
        if ($request->hasFile('file')){
            $files = $request->file('file');
            foreach ($files as $file) {
                $file->move($path, $file->getClientOriginalName());
            }
        }
        return $this->sendResponse($file->getClientOriginalName(),'OK');
    }

}
