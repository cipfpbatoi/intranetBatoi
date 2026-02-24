<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Grupo\GrupoService;

class GrupoController extends ApiResourceController
{
    private ?GrupoService $grupoService = null;

    protected $model = 'Grupo';

    public function __construct(?GrupoService $grupoService = null)
    {
        $this->grupoService = $grupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    public function list($id){

        $return = [];
        $grupo = $this->grupos()->find((string) $id);
        abort_unless($grupo !== null, 404);
        foreach ($grupo->Alumnos->sortBy('nameFull') as $alumno){
            $return[] = ['id' => $alumno->id, 'texto' => $alumno->nameFull, 'marked' => 1];
        }
        return ['data' => $return];
    }

}
