<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Services\School\ModuloGrupoService;


class AlumnoGrupoController extends ApiResourceController
{
    private ?GrupoService $grupoService = null;
    private ?ModuloGrupoService $moduloGrupoService = null;

    protected $model = 'AlumnoGrupo';

    public function __construct(?GrupoService $grupoService = null, ?ModuloGrupoService $moduloGrupoService = null)
    {
        $this->grupoService = $grupoService;
        $this->moduloGrupoService = $moduloGrupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function moduloGrupos(): ModuloGrupoService
    {
        if ($this->moduloGrupoService === null) {
            $this->moduloGrupoService = app(ModuloGrupoService::class);
        }

        return $this->moduloGrupoService;
    }

    private function alumnos($misgrupos)
    {
        $grupoIds = collect($misgrupos)->pluck('idGrupo')->filter()->all();
        if (empty($grupoIds)) {
            $grupoIds = collect($misgrupos)->pluck('codigo')->filter()->all();
        }
        if (empty($grupoIds)) {
            return [];
        }

        $registres = AlumnoGrupo::with('Alumno')
            ->whereIn('idGrupo', $grupoIds)
            ->get()
            ->filter(fn ($ag) => $ag->Alumno)     // fora els que no tenen alumne
            ->unique('idAlumno');                 // evita duplicats

        // Construeix [id => nom], ordena naturalment i torna format {id,name}
        $array = [];
        foreach ($registres as $ag) {
            $array[$ag->idAlumno] = $ag->Alumno->nameFull;
        }

        asort($array, SORT_NATURAL | SORT_FLAG_CASE);

        return collect($array)
            ->map(fn ($name, $id) => ['id' => $id, 'name' => $name])
            ->values()
            ->all();
    }

    public function show($id)
    {
            $cadena = (string) $id;
            if (strlen($cadena)==8){
                return $this->sendResponse(AlumnoGrupo::where('idAlumno',$cadena)->first(),'OK');
            } else {
                $migrupo = $this->grupos()->qTutor((string) $cadena);
                return $this->alumnos($migrupo);
            }
    }



    
    public function getModulo($dni,$modulo){
        //$migrupo = $this->grupos()->miGrupoModulo($dni,$modulo);
        $misgrupos = $this->moduloGrupos()->misModulos($dni, $modulo);
        return $this->alumnos($misgrupos);
    }

}
