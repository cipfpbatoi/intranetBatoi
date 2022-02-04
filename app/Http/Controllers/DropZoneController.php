<?php

namespace Intranet\Http\Controllers;

use DB;
use Illuminate\Http\Request;

/**
 * Class DropZoneController
 * @package Intranet\Http\Controllers
 */

class DropZoneController extends Controller
{
    public function storeAttached(Request $request){
        $id = $request->id;
        $modelo = $request->modelo;
        $path = storage_path()."/app/public/adjuntos/$modelo/$id";
        if ($request->hasFile('file')){
            $files = $request->file('file');
            foreach ($files as $file) {
                $file->move($path, $file->getClientOriginalName());
            }
        }
    }
    
}
