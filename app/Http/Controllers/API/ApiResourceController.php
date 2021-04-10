<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;
use Exception;
use Intranet\Http\Controllers\Controller;

class ApiResourceController extends Controller
{

//    protected $perfil;
//    
    protected $namespace = 'Intranet\\Entities\\';
    protected $model;
    protected $class;
    protected $resource;
    protected $guard='api';

    public function __construct()
    {
        $this->class = $this->namespace . $this->model;
    }

    public function index()
    {
        return $this->resource::collection($this->class::all());
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

    public function show($id)
    {
        return new $this->resource($this->class::find($id));
        
    }


    protected function sendResponse($result, $message)
    {
        return response()->json(['success'=>true,'data'=>$result]);
    }

    protected function sendError($error, $code = 404)
    {
        return response()->json(['success'=>false,'message'=>$error]);
    }

}
