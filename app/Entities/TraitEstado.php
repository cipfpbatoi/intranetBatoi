<?php

namespace Intranet\Entities;

use Illuminate\Support\Facades\Session;
use Intranet\Services\AdviseService;
use Intranet\Services\Gestor;

trait TraitEstado
{

    protected function crea_pestanas_estado($estados,$vista,$activa=null,$sustituye = null){
        if (!$activa){
            $activa = Session::get('pestana')?Session::get('pestana'):0;
        }
        foreach ($estados as $key => $estado) {
            $sustituto = ($key == $sustituye)?1:null;
            $this->panel->setPestana($estado, $key == $activa ? true : false, $vista,
                ['estado',$key],null,$sustituto,$this->parametresVista);
        }
    }

    private static function makeDocument($elemento){
        if ($elemento->fichero != ''){
            $gestor = new Gestor($elemento);
            $gestor->save([
                'tipoDocumento' => getClase($elemento),
                'rol'=> '2',
            ]);
        }
    }

    private static function dateResolve($elemento,$fecha){
        if (isset($elemento->fechasolucion)) {
            $elemento->fechasolucion = $fecha;
        }
    }


    public static function putEstado($id, $estado, $mensaje = null, $fecha = null)
    {
        $elemento = static::findOrFail($id);
        
        if ($fecha != null) {
            self::makeDocument($elemento);
            self::dateResolve($elemento,$fecha);
        }
      
        $elemento->estado = $estado;
        $elemento->save();
        $adviseService = new AdviseService($elemento,$mensaje);
        $adviseService->send();

        return ($elemento->estado);
    }

    public static function resolve($id,$mensaje = null)
    {
        return static::putEstado($id, config('modelos.' . getClass(static::class) . '.resolve'), $mensaje, Hoy());
    }

    public static function refuse($id, $mensaje = null)
    {
        return static::putEstado($id, config('modelos.' . getClass(static::class) . '.refuse'), $mensaje);
    }

    public static function _print($id)
    {
        if ( config('modelos.' . getClass(static::class) . '.print') == config('modelos.' . getClass(static::class) . '.resolve')) {
            return static::putEstado($id, config('modelos.' . getClass(static::class) . '.print'), '', Hoy());
        }  else {
            return static::putEstado($id, config('modelos.' . getClass(static::class) . '.print'));
        }
    }

    public static function getEstado($id)
    {
        return static::findOrFail($id)->estado;
    }

    public static function listos($estado = null)
    {
        $estado = $estado ? $estado : config('modelos.' . getClass(static::class) . '.print') - 1;
        return static::where('estado', '=', $estado)->get();
    }


}
