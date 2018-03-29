<?php

namespace Intranet\Entities;

use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Intranet\Entities\Documento;

trait TraitEstado
{

    public static function putEstado($id, $estado, $mensaje = null, $fecha = null)
    {
        $elemento = static::findOrFail($id);
        if ($fecha != null) {
            if ($elemento->fichero != '')
                Documento::crea($elemento, ['tipoDocumento' => getClase($elemento),'rol'=> '2']);
            if (isset($elemento->fechasolucion)) {
                $elemento->fechasolucion = $fecha;
            }
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
        return static::putEstado($id, config('modelos.' . getClass(static::class) . '.print'));
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
                    case 'vicedirector': avisa(config('constants.contacto.'.$quien),$explicacion,$enlace);break;
                    case 'jefeDepartamento' : avisa($this->Profesor->miJefe,$explicacion,$enlace);break;
                    default :
                        if (isset($this->$quien) && $this->$quien != ''){
                            
                            avisa($this->$quien, $explicacion, $enlace);
                        }
                }
            }
        }
    }

}
