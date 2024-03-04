<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Profesor;
use Intranet\Entities\GrupoTrabajo;
use Intranet\Entities\Miembro;
use Intranet\Http\Requests\GrupoTrabajoRequest;
use Intranet\Botones\BotonImg;
use Intranet\Http\Requests\GTProfesorRequest;


/**
 * Class GrupoTrabajoController
 * @package Intranet\Http\Controllers
 */
class GrupoTrabajoController extends ModalController
{
    const GRUPOTRABAJO_DETALLE = 'grupotrabajo.detalle';

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'GrupoTrabajo';
    /**
     * @var array
     */
    protected $gridFields = ['literal',  'objetivos'];


    public function store(GrupoTrabajoRequest $request)
    {
        $new = new GrupoTrabajo();
        $new->fillAll($request);
        return $this->redirect();
    }
    public function update(GrupoTrabajoRequest $request, $id)
    {
        GrupoTrabajo::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }


    /**
     * @return mixed
     */
    protected function seach()
    {
        return GrupoTrabajo::MisGruposTrabajo()->get();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detalle($id)
    {
        foreach (Profesor::select('apellido1', 'apellido2', 'nombre', 'dni')
                     ->OrderBy('apellido1')
                     ->OrderBy('apellido2')
                     ->get() as $profesor) {
            $tProfesores[$profesor->dni] = $profesor->nameFull;
        }

        $Gt = GrupoTrabajo::find($id);
        $sProfesores = $Gt->profesores()
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get(['dni', 'apellido1', 'apellido2', 'nombre', 'coordinador']);


        return view('grupotrabajo.miembros', compact('Gt', 'tProfesores', 'sProfesores'));
    }

    /**
     * @param Request $request
     * @param $gt_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function altaProfesor(GTProfesorRequest $request, $gtId)
    {
        Miembro::create($request->all());
        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupotrabajo' => $gtId]);
    }

    /**
     * @param $gt_id
     * @param $profesor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function borrarProfesor($gtId, $profesorId)
    {
        Miembro::where('idGrupoTrabajo', '=', $gtId)
                ->where('idProfesor', '=', $profesorId)
                ->where('coordinador', 0)
                ->delete();
        
        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupo_trabajo' => $gtId]);
    }

    /**
     * @param $grupo_id
     * @param $profesor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function coordinador($grupoId, $profesorId)
    {
        if ($this->removeCoord($grupoId)) {
            $this->addCoord($grupoId, $profesorId);
        }

        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupotrabajo' => $grupoId]);
    }

    /**
     * @param $grupo_id
     * @return bool
     */
    private function removeCoord($grupoId)
    {
        $coord = Miembro::where('idGrupoTrabajo', '=', $grupoId)
            ->where('coordinador', '=', '1')
            ->first();
        if ($coord) {
            $coord->coordinador = 0;
            $coord->save();
            return true;
        }
        return false;
    }

    /**
     * @param $grupo_id
     * @param $profesor_id
     */
    private function addCoord($grupoId, $profesorId)
    {
        $coord = Miembro::where('idGrupoTrabajo', '=', $grupoId)
            ->where('idProfesor', '=', $profesorId)
            ->first();
        $coord->coordinador = 1;
        $coord->save();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete']);
        $this->panel->setBoton('grid', new BotonImg('grupotrabajo.edit'));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                self::GRUPOTRABAJO_DETALLE,
                ['img' => 'fa-group', 'text' => 'participantes']
            )
        );
    }

}
