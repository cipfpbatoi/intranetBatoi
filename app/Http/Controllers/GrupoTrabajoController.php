<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Http\Request;
use Intranet\Entities\GrupoTrabajo;
use Intranet\Entities\Miembro;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\GrupoTrabajoRequest;
use Intranet\UI\Botones\BotonImg;
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
        $this->authorize('create', GrupoTrabajo::class);
        $this->persist($request);
        return $this->redirect();
    }

    /**
     * @param GrupoTrabajoRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(GrupoTrabajoRequest $request, $id)
    {
        $grupoTrabajo = $this->findModelOrFail(
            GrupoTrabajo::class,
            $id,
            'Grup de treball no trobat',
            ['grupo_trabajo_id' => $id]
        );
        $this->authorize('update', $grupoTrabajo);
        $this->persist($request, $id);
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
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detalle($id)
    {
        $Gt = $this->findModelOrFail(
            GrupoTrabajo::class,
            $id,
            'Grup de treball no trobat',
            ['grupo_trabajo_id' => $id]
        );
        $this->authorize('viewMembers', $Gt);
        foreach (app(ProfesorService::class)->allOrderedBySurname() as $profesor) {
            $tProfesores[$profesor->dni] = $profesor->nameFull;
        }

        $sProfesores = $Gt->profesores()
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get(['dni', 'apellido1', 'apellido2', 'nombre', 'coordinador']);


        return view('grupotrabajo.miembros', compact('Gt', 'tProfesores', 'sProfesores'));
    }

    /**
     * @param Request $request
     * @param int|string $gtId
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function altaProfesor(GTProfesorRequest $request, $gtId)
    {
        $grupoTrabajo = $this->findModelOrFail(
            GrupoTrabajo::class,
            $gtId,
            'Grup de treball no trobat',
            ['grupo_trabajo_id' => $gtId]
        );
        $this->authorize('manageMembers', $grupoTrabajo);
        Miembro::create($request->all());
        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupotrabajo' => $gtId]);
    }

    /**
     * @param int|string $gtId
     * @param int|string $profesorId
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function borrarProfesor($gtId, $profesorId)
    {
        $grupoTrabajo = $this->findModelOrFail(
            GrupoTrabajo::class,
            $gtId,
            'Grup de treball no trobat',
            ['grupo_trabajo_id' => $gtId]
        );
        $this->authorize('manageMembers', $grupoTrabajo);
        Miembro::where('idGrupoTrabajo', '=', $gtId)
            ->where('idProfesor', '=', $profesorId)
            ->where('coordinador', 0)
            ->delete();

        return redirect()->route(self::GRUPOTRABAJO_DETALLE, ['grupotrabajo' => $gtId]);
    }

    /**
     * @param int|string $grupoId
     * @param int|string $profesorId
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function coordinador($grupoId, $profesorId)
    {
        $grupoTrabajo = $this->findModelOrFail(
            GrupoTrabajo::class,
            $grupoId,
            'Grup de treball no trobat',
            ['grupo_trabajo_id' => $grupoId]
        );
        $this->authorize('manageMembers', $grupoTrabajo);
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

    /**
     * Elimina un grup de treball amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->findGrupoTrabajoOrFail($id));
        return parent::destroy($id);
    }

}
