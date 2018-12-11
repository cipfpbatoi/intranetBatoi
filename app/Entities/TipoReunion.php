<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Intranet\Entities;

use App;

class TipoReunion
{
    
    public static function allSelect($colectivo = null,$superUsuari=false)
    {
        $todos = [];
        foreach (config('tablas.tipoReunion') as $a) {
            if ($superUsuari || UserisAllow($a['rol']))
                if (!isset($a['ocultar']))
                    if (isset($colectivo)){
                        if ($a['colectivo']==$colectivo) $todos[$a['index']] = self::literal($a['index']);}
                    else    
                        $todos[$a['index']] = self::literal($a['index']);
        }
        return $todos;
    }

    public static function all()
    {
        return config('tablas.tipoReunion');
    }

    public static function get($index)
    {
        return config('tablas.tipoReunion')[$index];
    }

    public static function colectivo($index)
    {
        return config('tablas.tipoReunion')[$index]['colectivo'];
    }

    public static function convocatoria($index)
    {
        return config('tablas.tipoReunion')[$index]['convocatoria'];
    }
    public static function resumen($index)
    {
        return isset(config('tablas.tipoReunion')[$index]['resumen']) ? config('tablas.tipoReunion')[$index]['resumen'] : [];
    }

    public static function acta($index)
    {
        return config('tablas.tipoReunion')[$index]['acta'];
    }
    public static function numeracion($index){
         return config('tablas.tipoReunion')[$index]['numeracion'];
    }
            

    public static function ordenes($index)
    {
        return isset(config('tablas.tipoReunion')[$index]['ordenes']) ? config('tablas.tipoReunion')[$index]['ordenes'] : [];
    }

    public static function getSelect($index)
    {
        return self::get($index)['select'];
    }
    

    public static function literal($index)
    {
        return App::getLocale(session('lang')) == 'es' ? config('tablas.tipoReunion')[$index]['cliteral'] : config('tablas.tipoReunion')[$index]['vliteral'];
    }
    
    

}
