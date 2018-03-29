<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use DB;

class CentroController extends ApiBaseController
{

    protected $model = 'Centro';

    public function fusionar(Request $request)
    {

        if (count($request->fusion) > 1) {
            DB::transaction(function() use ($request) {
                $centroQ = Centro::findOrFail($request->fusion[0]);
                foreach ($request->fusion as $codiCentre) {
                    if ($codiCentre != $centroQ->id) {
                        $centro = Centro::findOrFail($codiCentre);
                        if ($centroQ->nombre == '')
                            $centroQ->nombre = $centro->nombre;
                        if ($centroQ->horarios == '')
                            $centroQ->horarios = $centro->horarios;
                        if ($centroQ->direccion == '')
                            $centroQ->direccion = $centro->direccion;
                        if ($centroQ->localidad == '')
                            $centroQ->nombre = $centro->localidad;
                        if ($centroQ->email == '')
                            $centroQ->email = $centro->email;
                        if ($centroQ->telefono == '')
                            $centroQ->telefono = $centro->telefono;
                        $centroQ->observaciones .= '.' . $centro->observaciones;
                        foreach ($centro->colaboraciones as $colaboracion) {
                            if ($colaboracionQ = Colaboracion::where('idCentro', $centroQ->id)
                                    ->where('idCiclo', $colaboracion->idCiclo)
                                    ->first()) {
                                $colaboracionQ->puestos += $colaboracion->puestos;
                                if ($colaboracionQ->contacto == '') $colaboracionQ->contacto = $colaboracion->contacto;
                                if ($colaboracionQ->tutor == '') $colaboracionQ->tutor = $colaboracion->tutor;
                                if ($colaboracionQ->telefono == '') $colaboracionQ->telefono = $colaboracion->telefono;
                                if ($colaboracionQ->instructor == '') $colaboracionQ->instructor = $colaboracion->instructor;
                                if ($colaboracionQ->dni == '') $colaboracionQ->dni = $colaboracion->dni;
                                if ($colaboracionQ->email == '') $colaboracionQ->email = $colaboracion->email;
                                $colaboracionQ->save();
                                foreach ($colaboracion->fcts as $fct){
                                   $fct->idColaboracion = $colaboracionQ->id;
                                   $fct->save();
                                }
                            } else {
                                $colaboracion->idCentro = $centroQ->id;
                                $colaboracion->save();
                            }
                        }
                        $centro->delete();
                    }
                }
                $centroQ->save();
                return $this->sendResponse($centroQ, 'Fusionados');
            });
        }
        return $this->sendError('No hay seleccionados', 200);
    }

}
