<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Controller;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use DB;

class CentroController extends ApiResourceController
{

    protected $model = 'Centro';

    public function fusionar(Request $request)
    {

        if (isset($request->fusion) && count($request->fusion) > 1) {
            DB::transaction(function () use ($request) {
                $centroQ = Centro::findOrFail($request->fusion[0]);
                foreach ($request->fusion as $codiCentre) {
                    if ($codiCentre != $centroQ->id) {
                        $this->fusion($codiCentre, $centroQ);
                    }
                }
                $centroQ->save();
                return $this->sendResponse($centroQ, 'Fusionados');
            });
        }
        return $this->sendError('No hay seleccionados', 200);
    }


    /**
     * @param $codiCentre
     * @param $centroQ
     */
    private function fusion($codiCentre, &$centroQ): void
    {
        $centro = $this->fusionCenter($codiCentre, $centroQ);
        foreach ($centro->colaboraciones as $colaboracion) {
            if ($colaboracionQ = Colaboracion::where('idCentro', $centroQ->id)
                ->where('idCiclo', $colaboracion->idCiclo)
                ->first()) {
                $this->fusionColaboration($colaboracion, $colaboracionQ);
            } else {
                $colaboracion->idCentro = $centroQ->id;
                $colaboracion->save();
            }
        }
        $centro->delete();
    }

    /**
     * @param $codiCentre
     * @param $centroQ
     * @return mixed
     */
    private function fusionCenter($codiCentre, &$centroQ)
    {
        $centro = Centro::findOrFail($codiCentre);
        if ($centroQ->nombre == '') {
            $centroQ->nombre = $centro->nombre;
        }
        if ($centroQ->horarios == '') {
            $centroQ->horarios = $centro->horarios;
        }
        if ($centroQ->direccion == '') {
            $centroQ->direccion = $centro->direccion;
        }
        if ($centroQ->localidad == '') {
            $centroQ->nombre = $centro->localidad;
        }
        if ($centroQ->email == '') {
            $centroQ->email = $centro->email;
        }
        if ($centroQ->telefono == '') {
            $centroQ->telefono = $centro->telefono;
        }
        $centroQ->observaciones .= '.' . $centro->observaciones;
        return $centro;
    }

    /**
     * @param $colaboracion
     * @param $colaboracionQ
     */
    private function fusionColaboration($colaboracion, $colaboracionQ): void
    {
        $colaboracionQ->puestos += $colaboracion->puestos;
        if ($colaboracionQ->contacto == '') {
            $colaboracionQ->contacto = $colaboracion->contacto;
        }
        if ($colaboracionQ->tutor == '') {
            $colaboracionQ->tutor = $colaboracion->tutor;
        }
        if ($colaboracionQ->telefono == '') {
            $colaboracionQ->telefono = $colaboracion->telefono;
        }
        if ($colaboracionQ->email == '') {
            $colaboracionQ->email = $colaboracion->email;
        }
        $colaboracionQ->save();
        foreach ($colaboracion->fcts as $fct) {
            $fct->idColaboracion = $colaboracionQ->id;
            $fct->save();
        }
    }



}
