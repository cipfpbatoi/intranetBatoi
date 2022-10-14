<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Profesor;
use Intranet\Entities\GrupoTrabajo;
use Intranet\Entities\Miembro;
use Response;
use Intranet\Botones\BotonImg;


/**
 * Class GrupoTrabajoController
 * @package Intranet\Http\Controllers
 */
class GrupoTrabajoController extends IntranetController
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
    /**
     * @var bool
     */
    protected $modal = true;


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
        $sProfesores = $Gt->profesores()->orderBy('apellido1')->orderBy('apellido2')->get(['dni', 'apellido1', 'apellido2', 'nombre', 'coordinador']);


        return view('grupotrabajo.miembros', compact('Gt', 'tProfesores', 'sProfesores'));
    }

    /**
     * @param Request $request
     * @param $gt_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function altaProfesor(Request $request, $gt_id)
    {
        Miembro::create($request->all());
        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupotrabajo' => $gt_id]);
    }

    /**
     * @param $gt_id
     * @param $profesor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function borrarProfesor($gt_id, $profesor_id)
    {
        Miembro::where('idGrupoTrabajo', '=', $gt_id)
                ->where('idProfesor', '=', $profesor_id)
                ->where('coordinador',0)
                ->delete();
        
        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupo_trabajo' => $gt_id]);
    }

    /**
     * @param $grupo_id
     * @param $profesor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function coordinador($grupo_id, $profesor_id)
    {
        if ($this->removeCoord($grupo_id)) {
            $this->addCoord($grupo_id, $profesor_id);
        }

        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupotrabajo' => $grupo_id]);
    }

    /**
     * @param $grupo_id
     * @return bool
     */
    private function removeCoord($grupo_id)
    {
        $coord = Miembro::where('idGrupoTrabajo', '=', $grupo_id)
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
    private function addCoord($grupo_id, $profesor_id){
        $coord = Miembro::where('idGrupoTrabajo', '=', $grupo_id)
            ->where('idProfesor', '=', $profesor_id)
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
        $this->panel->setBoton('grid', new BotonImg('#', ['img' => 'fa-pencil', 'class' => 'editGrupo', 'text' => 'edita']));
        $this->panel->setBoton('grid', new BotonImg(self::GRUPOTRABAJO_DETALLE, ['img' => 'fa-group', 'text' => 'participantes']));
    }

}
