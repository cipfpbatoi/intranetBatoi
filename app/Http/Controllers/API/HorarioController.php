<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;

class HorarioController extends ApiBaseController
{

    protected $model = 'Horario';
    
    
     public function show($cadena,$send=true)
    {
        if (!strpos($cadena, '=')&&!strpos($cadena, '>')&&!strpos($cadena, '<')&&!strpos($cadena, ']')&&!strpos($cadena, '[')&&!strpos($cadena, '!'))
            $data = $this->class::find($cadena);
        else {
            $filtros = explode('&', $cadena);
            if (!strpos($cadena, 'ields='))
                $data = $this->class::all();
            else {
                foreach ($filtros as $filtro) {
                    $campos = explode('=', $filtro);
                    $value = $campos[0];
                    $key = $campos[1];
                    if ($value == 'fields')
                        $data = $this->fields($key);
                }
            }
           
            foreach ($filtros as $filtro) {
                foreach (['=','<','>',']','[','!'] as $operacion){
                    $campos = explode($operacion, $filtro);
                    
                    if (count($campos)==2){
                        
                        $value = $campos[0];
                        $key = $campos[1];
                        if ($value != 'fields')
                            $data = $data->filter(function ($filtro) use ($value, $key,$operacion) {
                                if ($key === 'null') $key = null;
                                switch ($operacion){
                                    case '=' : {
                                        if ($value == 'idProfesor')
                                           if (($sustituto = Profesor::findOrFail($key)->sustituye_a) != ' ')
                                               return $filtro->$value == $key || $filtro->$value == $sustituto;
                                           else
                                               return $filtro->$value == $key;
                                        else return $filtro->$value == $key;
                                        break;
                                    }
                                    case '>' : return $filtro->$value > $key; break;
                                    case '<' : return $filtro->$value < $key; break;
                                    case ']' : return $filtro->$value >= $key; break;
                                    case '[' : return $filtro->$value <= $key; break;
                                    case '!' : return $filtro->$value != $key; break;
                                }
                                
                            });
                    }
                }
            }
        }
        if ($send) return $this->sendResponse($data, 'OK');
        else return $data;        
    }
    
    public function HorariosDia($fecha){
        $data = [];
        $profes = Profesor::select('dni')->Activo()->get();
        foreach ($profes as $profe) {
            $horario = Horario::Primera($profe->dni,$fecha)->orderBy('sesion_orden')->get();
            if (isset($horario->first()->desde)) {
                $data[$profe->dni] = $horario->first()->desde . " - " . $horario->last()->hasta;
            } else
                $data[$profe->dni] = '';
        }
        return $this->sendResponse($data, 'OK');
    }
    
    public function getChange($dni){
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json'))
            if ($data = Storage::disk('local')->get('/horarios/'.$dni.'.json'))
                return $this->sendResponse($data,'OK');
            else 
                return $this->sendError('No hi han canvis');
        else
           return $this->sendError('No hi ha fitxer'); 
    }
    
    public function Change(Request $request,$dni){
        if (Storage::disk('local')->put('/horarios/'.$dni.'.json', $request->data))
                return $this->sendResponse('Guardado Correctament','OK');
        else
            return $this->sendError('No se ha podido guardar');
        
    }
}
