<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Material;
use Intranet\Entities\MaterialBaja;

use Intranet\Http\Resources\MaterialResource;
use Jenssegers\Date\Date;
use Yajra\DataTables\DataTables;

class MaterialController extends ApiResourceController
{
    const ROLES_ROL_DIRECCION = 'roles.rol.direccion';
    protected $model = 'Material';


    function getMaterial($espacio)
    {
        return response()->json(Material::where('espacio', $espacio)->get());
    }

    private function getInventario($espai = null)
    {
        if (esRol(apiAuthUser($_GET['api_token'])->rol, config(self::ROLES_ROL_DIRECCION))) {
            $data = Material::where('inventariable', 1)
                ->where('espacio', '<>', 'INVENT')
                ->where('estado', '<', 3)
                ->whereNotNull('articulo_lote_id')
                ->when($espai, function ($query) use ($espai) {
                    return $query->where('espacio', $espai);
                })
                ->get();
        } else {
            $data = Material::whereHas('espacios', function ($query) {
                $query->where('idDepartamento', apiAuthUser($_GET['api_token'])->departamento);
            })->where('inventariable', 1)
                ->where('espacio', '<>', 'INVENT')
                ->where('estado', '<', 3)
                ->whereNotNull('articulo_lote_id')
                ->when($espai, function ($query) use ($espai) {
                    return $query->where('espacio', $espai);
                })
                ->get();
        }

        return $this->sendResponse(MaterialResource::collection($data), 'OK');
    }

    public function espai($espai)
    {
        return $this->getInventario($espai);
    }

    public function inventario()
    {
        return $this->getInventario();
    }

    function index()
    {
        $data = Material::where('inventariable', 0)->get();
        return $this->sendResponse($data, 'OK');
    }

    public function put(Request $request)
    {
        $material = Material::findOrFail($request->id);
        $anterior = $material->unidades;
        $material->unidades = $request->unidades;
        $material->save();
        $material->explicacion = $request->explicacion;
        $material->propiedad = 'unidades';
        $material->anterior = $anterior;
    }

    public function putUnidades(Request $request)
    {

        $material = Material::findOrFail($request->id);
        $anterior = $material->unidades;
        $material->unidades = $request->unidades;
        $material->save();
        $aviso = 'El material '.$material->descripcion. " ubicat a l'espai ".$material->espacio." ha canviat de ".$anterior.' a '.$material->unidades.' unitats: '.$request->explicacion.".";
        avisa(config('avisos.material'),$aviso,'#','SISTEMA');
        
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

    public function putUbicacion(Request $request)
    {
        $material = Material::findOrFail($request->id);
        $user = apiAuthUser($request->api_token);
        $esadmin = esRol($user->rol, 2);
        $missatge = '';
        try {
            $anterior = $material->espacio;
            if ($esadmin) {
                $material->espacio = $request->ubicacion;
                $material->save();
                $missatge = $request->ubicacion;
                $aviso = 'El material '.$material->descripcion." ubicat a l'espai ".$anterior." ha canviat a ".$material->espacio.': '.$request->explicacion.".";
                avisa(config('avisos.material'), $aviso, '#', 'SISTEMA');
            } else {
                $materialBaja = new MaterialBaja(
                    [
                        'idMaterial' => $material->id,
                        'idProfesor' => $user->dni,
                        'motivo' => $request->explicacion,
                        'estado' => '0',
                        'tipo' => '1' ,
                        'nuevoEstado' => $request->ubicacion,
                    ]
                );
                $materialBaja->save();
                $missatge = 'Proposta Nova ubicació';
                $aviso = 'El material '.$material->descripcion." ubicat a l'espai ".$anterior." vol canviar a ".$request->ubicacion.': '.$request->explicacion.".";
                avisa(config('avisos.material'), $aviso, '#', 'SISTEMA');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

        return $this->sendResponse(['updated' => $missatge], 'OK');
    }

    public function putEstado(Request $request)
    {
        $material = Material::findOrFail($request->id);
        $user = apiAuthUser($request->api_token);
        $esadmin = esRol($user->rol, 2);
        $missatge = '';
        try {
            if ($esadmin) {
                $material->estado = $request->estado;
                if ($material->estado == 3) {
                    $material->fechaBaja = Hoy();
                    $materialBaja = new MaterialBaja(
                        [
                            'idMaterial' => $material->id,
                            'idProfesor' => $user->dni,
                            'motivo' => $request->explicacion,
                            'estado' => '1'
                        ]
                    );
                    $materialBaja->save();
                    $missatge = 'Baixa';
                }
                $material->save();
            } else {
                if ($request->estado == 3) {
                    $materialBaja = new MaterialBaja(
                        [
                            'idMaterial' => $material->id,
                            'idProfesor' => $user->dni,
                            'motivo' => $request->explicacion,
                            'estado' => '0'
                        ]
                    );
                    $materialBaja->save();
                    $aviso = 'El material '.$material->descripcion. " ubicat a l'espai ".$material->espacio." ha sigut proposat per a Baixa : ".$request->explicacion.".";
                    avisa(config('avisos.material'), $aviso, '#', 'SISTEMA');
                    $missatge = 'Proposta de baixa';
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

        return $this->sendResponse(['updated' => $missatge], 'OK');
    }

    private function isAdministrator()
    {
        return apiAuthUser()->hasRole('admin');
    }

    public function putInventario(Request $request)
    {
        $fecha = new Date();

        $material = Material::findOrFail($request->id);
        if ($request->inventario == 'true') {
            $material->fechaultimoinventario = $fecha->format('Y-m-d');
        } else {
            $material->fechaultimoinventario = "1970-01-01";
        }
        $material->save();
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

}
