<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Http\Requests\AlumnoResultadoStoreRequest;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoResultado;
use Intranet\Entities\Resultado;
use Intranet\Exceptions\NotFoundDomainException;
use Styde\Html\Facades\Alert;

/**
 * Class PanelSeguimientoAlumnosController
 * @package Intranet\Http\Controllers
 */
class PanelSeguimientoAlumnosController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'AlumnoResultado';
    protected $redirect = 'PanelSeguimientoAlumnosController@indice';

    /**
     * @param int|string $search
     * @throws NotFoundDomainException
     * @return \Illuminate\View\View
     */
    public function indice($search)
    {
        try {
            $resultado = Resultado::findOrFail((int) $search);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Resultat no trobat', ['resultado_id' => $search]);
        }
        $this->authorize('view', $resultado);
        $elemento = $resultado->moduloGrupo;
         
        $renunciaIds = $this->renunciaAlumnoIds($resultado);
        $resultados = AlumnoResultado::where('idModuloGrupo', $resultado->idModuloGrupo)
            ->whereIn('idAlumno', $renunciaIds->all())
            ->get();
        $alumnes = $this->createWithDefaultValues(['idModuloGrupo' => $resultado->idModuloGrupo])->getidAlumnoOptions();
        if ($alumnes !== []) {
            $alumnes = array_intersect_key($alumnes, array_flip($renunciaIds->all()));
        }
        return view('seguimiento.index', compact('elemento',  'alumnes', 'resultados'));
    }




    /*
     * store (Request) return redirect
     * guarda els valors del formulari
     */
    /**
     * @param Request $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, (new AlumnoResultadoStoreRequest())->rules());
        try {
            $resultado = Resultado::where('idModuloGrupo', $request->idModuloGrupo)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Resultat no trobat', ['modulo_grupo_id' => $request->idModuloGrupo]);
        }
        $this->authorize('update', $resultado);
        $renunciaIds = $this->renunciaAlumnoIds($resultado);
        if (!$renunciaIds->contains((string) $request->idAlumno)) {
            Alert::warning("Només es poden afegir notes per alumnat amb renúncia en FCT.");
            return back();
        }
        $this->realStore($request);
        return back();
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id){
        try {
            $alumnoResultado = AlumnoResultado::findOrFail((int) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Resultat d\'alumne no trobat', ['alumno_resultado_id' => $id]);
        }
        try {
            $resultado = Resultado::where('idModuloGrupo', $alumnoResultado->idModuloGrupo)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Resultat no trobat', ['modulo_grupo_id' => $alumnoResultado->idModuloGrupo]);
        }
        $this->authorize('update', $resultado);
        $this->search = (int) $resultado->id;
        parent::destroy($id);
        return back();
    }

    /**
     * Retorna llistat d'alumnes amb renúncia en FCT per al grup del mòdul.
     *
     * @return Collection<int, string>
     */
    private function renunciaAlumnoIds(Resultado $resultado): Collection
    {
        $grupo = $resultado->moduloGrupo?->Grupo;
        if (!$grupo) {
            return collect();
        }
         

        return AlumnoFct::query()
            ->Grupo($grupo)
            ->where('calificacion', 3)
            ->pluck('idAlumno');
    }
}
