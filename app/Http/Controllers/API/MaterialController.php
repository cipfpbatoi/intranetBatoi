<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Material;
use Intranet\Entities\MaterialBaja;

use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Resources\MaterialResource;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Log;

/**
 * Controlador API de materials.
 */
class MaterialController extends ApiResourceController
{
    const ROLES_ROL_DIRECCION = 'roles.rol.direccion';
    protected $model = 'Material';

    function getMaterial($espacio)
    {
        return response()->json(Material::where('espacio', $espacio)->get());
    }

    private function getInventario(Request $request, $espai = null)
    {
        $user = $this->resolveApiUser($request);
        if (!$user) {
            return $this->sendError('Unauthorized', 401);
        }

        if (esRol($user->rol, config(self::ROLES_ROL_DIRECCION))) {
            $data = Material::where('inventariable', 1)
                ->where('espacio', '<>', 'INVENT')
                ->where('estado', '<', 3)
                ->whereNotNull('articulo_lote_id')
                ->when($espai, function ($query) use ($espai) {
                    return $query->where('espacio', $espai);
                })
                ->get();
        } else {
            $data = Material::whereHas('espacios', function ($query) use ($user) {
                $query->where('idDepartamento', $user->departamento);
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

    public function espai(Request $request, $espai)
    {
        return $this->getInventario($request, $espai);
    }

    public function inventario(Request $request)
    {
        return $this->getInventario($request);
    }

    function index()
    {
        $data = Material::where('inventariable', 0)->get();
        return $this->sendResponse($data, 'OK');
    }

    /**
     * @param Request $request
     * @throws NotFoundDomainException
     * @return void
     */
    public function put(Request $request)
    {
        $material = $this->findModelOrFail(Material::class, $request->id, 'Material no trobat', ['material_id' => $request->id]);
        $anterior = $material->unidades;
        $material->unidades = $request->unidades;
        $material->save();
        $material->explicacion = $request->explicacion;
        $material->propiedad = 'unidades';
        $material->anterior = $anterior;
    }

    /**
     * @param Request $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function putUnidades(Request $request)
    {

        $material = $this->findModelOrFail(Material::class, $request->id, 'Material no trobat', ['material_id' => $request->id]);
        $anterior = $material->unidades;
        $material->unidades = $request->unidades;
        $material->save();
        $aviso = 'El material '.$material->descripcion. " ubicat a l'espai ".$material->espacio." ha canviat de ".$anterior.' a '.$material->unidades.' unitats: '.$request->explicacion.".";
        avisa(config('avisos.material'),$aviso,'#','SISTEMA');
        
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

    /**
     * @param Request $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function putUbicacion(Request $request)
    {
        $material = $this->findModelOrFail(Material::class, $request->id, 'Material no trobat', ['material_id' => $request->id]);
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
            report($e);
            Log::error('Error actualitzant estats o ubicació de material.', [
                'material_id' => $material->id ?? null,
                'espai_destinacio' => $request->ubicacion ?? null,
                'error' => $e->getMessage(),
            ]);
            return $this->sendError($e->getMessage(), 500);
        }

        return $this->sendResponse(['updated' => $missatge], 'OK');
    }

    /**
     * @param Request $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function putEstado(Request $request)
    {
        $material = $this->findModelOrFail(Material::class, $request->id, 'Material no trobat', ['material_id' => $request->id]);
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
            report($e);
            Log::error('Error proposant baixa de material.', [
                'material_id' => $material->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return $this->sendError($e->getMessage(), 500);
        }

        return $this->sendResponse(['updated' => $missatge], 'OK');
    }

    private function resolveApiUser(Request $request)
    {
        $guardUser = $request->user('api');
        if ($guardUser) {
            return $guardUser;
        }

        $token = (string) ($request->query('api_token')
            ?? $request->input('api_token')
            ?? '');

        if ($token === '') {
            return null;
        }

        return apiAuthUser($token);
    }

    /**
     * @param Request $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function putInventario(Request $request)
    {
        $fecha = new Date();

        $material = $this->findModelOrFail(Material::class, $request->id, 'Material no trobat', ['material_id' => $request->id]);
        if ($request->inventario == 'true') {
            $material->fechaultimoinventario = $fecha->format('Y-m-d');
        } else {
            $material->fechaultimoinventario = "1970-01-01";
        }
        $material->save();
        return $this->sendResponse(['updated' => json_encode($request)], 'OK');
    }

}
