<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Application\Grupo\GrupoService;
use Illuminate\Http\Request;
use Intranet\Http\Resources\AlumnoFctControlResource;
use Intranet\Http\Resources\AlumnoFctResource;


class AlumnoFctController extends ApiResourceController
{
    private ?GrupoService $grupoService = null;
    private ?AlumnoFctService $alumnoFctService = null;

    protected $model = 'AlumnoFct';

    public function __construct(?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null)
    {
        $this->grupoService = $grupoService;
        $this->alumnoFctService = $alumnoFctService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function alumnoFcts(): AlumnoFctService
    {
        if ($this->alumnoFctService === null) {
            $this->alumnoFctService = app(AlumnoFctService::class);
        }

        return $this->alumnoFctService;
    }

    public function indice($grupo)
    {
        $grup = $this->grupos()->find((string) $grupo);
        abort_unless($grup !== null, 404);
        $data = AlumnoFctControlResource::collection($this->alumnoFcts()->byGrupoEsFct((string) $grup->codigo));

        return $this->sendResponse($data, 'OK');
    }

    public function dual($grupo)
    {
        $grup = $this->grupos()->find((string) $grupo);
        abort_unless($grup !== null, 404);
        $data = AlumnoFctControlResource::collection($this->alumnoFcts()->byGrupoEsDual((string) $grup->codigo));

        return $this->sendResponse($data, 'OK');
    }

    public function update(Request $request, $id)
    {
        $registro = $this->alumnoFcts()->findOrFail((int) $id);
        if (isset($request->pg0301)) {
            $registro->pg0301 = $request->pg0301==='true'?1:0;
        }
        if (isset($request->a56)) {
            $registro->a56 = $request->a56 === 'true' ? 1 : 0;
        }
        $registro->save();
        return $this->sendResponse(['updated' => true], 'OK');
    }

    public function show($id)
    {
        $registro = $this->alumnoFcts()->findOrFail((int) $id);
        return $this->sendResponse(new AlumnoFctResource($registro), 'OK');
    }



}
