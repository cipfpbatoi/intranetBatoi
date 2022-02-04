<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Expediente;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;

class DropZoneController extends ApiBaseController
{
    public function getFiles($modelo,$id){
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

    public function removeFile($modelo,$id,$file){
        $path = storage_path()."/app/public/adjuntos/$modelo/$id/$file";
        if (is_file($path)) {
            unlink($path);
        }
        return $this->sendResponde([],'OK');
    }

}
