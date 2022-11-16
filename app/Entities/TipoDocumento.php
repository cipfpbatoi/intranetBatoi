<?php

namespace Intranet\Entities;

class TipoDocumento
{
    
    public static function allPestana()
    {
        if (esRol(authUser()->rol,config('roles.rol.qualitat'))) return ['Millora'=>trans('messages.buttons.Millora')];
        $todos = [];
        foreach (config('tablas.tipoDocumento') as $a) {
            if (userIsAllow($a['rol'])&& $a['pestana'])
                $todos[$a['index']] = trans('messages.buttons.'.$a['index']);
        }
        return $todos;
    }
    public static function allDocuments()
    {
        $todos = [];
        foreach (config('tablas.tipoDocumento') as $a) {
            if (userIsAllow($a['rol'])&& $a['pestana'])
                $todos[] = $a['index'];
        }
        return $todos;
    }
    
    public static function allRol($grupo)
    {
        $todos = [];
        foreach (config('tablas.tipoDocumento') as $a) {
            if (userIsAllow($a['rol'])&& $a['grupo']==$grupo)
                $todos[$a['index']] = $a['rol'];
        }
        return $todos;
    }
    public static function rol($index)
    {
       foreach (config('tablas.tipoDocumento') as $a) {
            if  ($a['index']==$index) return $a['rol'];
        } 
    }

    public static function all($grupo)
    {
        $todos = [];
        foreach (config('tablas.tipoDocumento') as $a) {
            if (userIsAllow($a['rol'])&& $a['grupo']==$grupo)
                $todos[$a['index']] = $a['index'];
        }
        return $todos;
    }

    public static function get($index)
    {
        return config('tablas.tipoDocumento')[$index];
    }

    

}
  
    
