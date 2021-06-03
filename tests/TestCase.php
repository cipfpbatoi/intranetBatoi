<?php

namespace Tests;

use Intranet\Entities\Profesor;

use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Console\Kernel;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    public $baseUrl = 'http://localhost';
    
    public function direccionUser(){
        return Profesor::find('007864107Q');
    }
    public function defaultUser(){
        return Profesor::find('021652470V');
    }  
    public function defaultTutor(){
        return Profesor::find('021652470V');
    }
    public function siguiente($tabla){
       $ultim = DB::select("SELECT `AUTO_INCREMENT` AS ultimo FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = 'intranet' AND TABLE_NAME   = '$tabla'");
       return $ultim[0]->ultimo;
    }
    public function newModel($model,array $fields)
    {
        $modelo = new $model;
        foreach ($fields as $name => $value){
            $modelo->$name = $value;
        }
        return $modelo;
    }
}
