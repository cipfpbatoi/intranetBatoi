<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Profesor\ProfesorService;
use Illuminate\Http\Request;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;
use Exception;
use Intranet\Http\Controllers\Controller;

class ApiBaseController extends Controller
{

    protected $namespace = 'Intranet\\Entities\\';
    protected $model;
    protected $class;
    protected $rules;
    protected $guard='api';
    private ?ProfesorService $profesorService = null;

    public function __construct()
    {
        $this->class = $this->namespace . $this->model;
    }

    public function index()
    {
        $data = $this->class::all();
        return $this->sendResponse($data, 'OK');
    }

    public function destroy($id)
    {
        $this->class::destroy($id);
        return $this->sendResponse(['deleted' => true], 'OK');
    }

    public function store(Request $request)
    {
        try {
            $this->class::create($request->all());
            return $this->sendResponse(['created' => true], 'OK');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function ApiUser(Request $request){
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService->findByApiToken((string) $request->api_token);
    }

    public function update(Request $request, $id)
    {
        try {
            $registro = $this->class::find($id);
            $registro->update($request->all());
            $registro->save();
            return $this->sendResponse(['updated' => true], 'OK');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function edit($id)
    {
        return $this->sendResponse($this->class::find($id));
    }

    public function show($cadena, $send=true)
    {
        if (!strpos($cadena, '=') && !strpos($cadena, '>')&&!strpos($cadena, '<')&&!strpos($cadena, ']')&&!strpos($cadena, '[')) {
            $data = $this->class::find($cadena);
        }
        else {
            $filtros = explode('&', $cadena);
            if (!strpos($cadena, 'ields=')) {
                $data = $this->class::all();
            }
            else {
                foreach ($filtros as $filtro) {
                    $campos = explode('=', $filtro);
                    $value = $campos[0];
                    $key = $campos[1];
                    if ($value == 'fields') {
                        $data = $this->fields($key);
                    }
                }
            }

            foreach ($filtros as $filtro) {
                foreach (['=','<','>',']','['] as $operacion){
                    $campos = explode($operacion, $filtro);
                    
                    if (count($campos)==2){
                        $value = $campos[0];
                        $key = $campos[1];
                        if ($value != 'fields') {
                            $data = $data->filter(function ($filtro) use ($value, $key, $operacion) {
                                switch ($operacion) {

                                    case '=' :
                                        return $filtro->$value == $key;
                                        break;
                                    case '>' :
                                        return $filtro->$value > $key;
                                        break;
                                    case '<' :
                                        return $filtro->$value < $key;
                                        break;
                                    case ']' :
                                        return $filtro->$value >= $key;
                                        break;
                                    case '[' :
                                        return $filtro->$value <= $key;
                                        break;
                                }
                            });
                        }
                    }
                }
            }
        }


        if ($send) {
            return $this->sendResponse($data, 'OK');
        }
        else {
            return $data;
        }
        
    }

    protected function fields($fields)
    {
        $campos = explode(',', $fields);
        foreach ($campos as $campo) {
            $value[] = $campo;
        }
        return $this->class::all($value);
    }

    protected function sendResponse($result, $message=null)
    {
        return response()->json(['success'=>true,'data'=>$result]);
    }

    protected function sendError($error, $code = 404)
    {
        return response()->json(['success'=>false,'message'=>$error]);
    }

    protected function sendFail($error,$code = 400)
    {
        return response()->json($error,$code);
    }

}
