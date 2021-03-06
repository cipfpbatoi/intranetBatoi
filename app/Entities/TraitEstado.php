<?php

namespace Intranet\Entities;

use Illuminate\Support\Facades\Session;
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
        $elemento->informa($mensaje);
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

    // a REVISAR
    protected function informa($mensaje)
    {
        foreach (config('modelos.' . getClase($this) . '.avisos') as $quien => $cuando) {
            
            if (in_array($this->estado, $cuando)) {
                // clase
                $explicacion = getClase($this) . ' ' . primryKey($this) . ' ' . trans('models.' . getClase($this) . '.' . $this->estado) . ": ";
               
                if (isset($this->descriptionField)) {
                    $descripcion = $this->descriptionField;
                    $explicacion .= mb_substr(str_replace(array("\r\n", "\n", "\r"),' ',$this->$descripcion),0,50) . ". ";
                }
                //mensaje que pasa el usuario
               
                $explicacion .= isset($mensaje) ? $mensaje : '';
                
                // mensaje específico del fichero de modelos
                $explicacion .= blankTrans("models." . $this->estado . "." . getClase($this));
                
                //enllaç al element
                $enlace = "/" . strtolower(getClase($this)) . "/" . $this->id;
                $enlace .= $this->estado < 2  ? "/edit" : "/show";
                
                switch ($quien){
                    case 'Creador': avisa($this->Creador(), $explicacion, $enlace);break;
                    case 'director': 
                    case 'jefeEstudios':
                    case 'secretario' : 
                    case 'orientador' :    
                    case 'vicedirector': 
                        if (is_array(config('contacto.'.$quien))) {
                            foreach (config('contacto.' . $quien) as $id) {
                                avisa($id, $explicacion, $enlace);
                            }
                        }
                        else {
                            avisa(config('contacto.' . $quien), $explicacion, $enlace);
                        }
                        break;
                    case 'jefeDepartamento' : isset($this->Profesor->dni)?avisa($this->Profesor->miJefe,$explicacion,$enlace):avisa(AuthUser()->miJefe,$explicacion,$enlace);break;
                    default :
                        if (isset($this->$quien) && $this->$quien != ''){
                            
                            avisa($this->$quien, $explicacion, $enlace);
                        }
                }
            }
        }
    }

}
