<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Ciclo;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\CicloRequest;

/**
 * Class CicloController
 * @package Intranet\Http\Controllers
 */
class CicloController extends ModalController
{
    const ADMINISTRADOR = 'roles.rol.administrador';

    /**
     * @var string
     */
    protected $model = 'Ciclo';
    /**
     * @var array
     */
    protected $gridFields = [ 'id','ciclo','literal','Xdepartamento','Xtipo'];

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return Ciclo
     */
    private function findCicloOrFail($id): Ciclo
    {
        try {
            return Ciclo::findOrFail((int) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Cicle no trobat', ['ciclo_id' => $id]);
        }
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('ciclo.create', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.show', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.edit', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.delete', ['roles' => config(self::ADMINISTRADOR)]));
    }

    public function store(CicloRequest $request)
    {
        $this->authorize('create', Ciclo::class);
        $this->persist($request);
        return $this->redirect();
    }

    /**
     * @param CicloRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(CicloRequest $request, $id)
    {
        $this->authorize('update', $this->findCicloOrFail($id));
        $this->persist($request, $id);
        if ($file = $request->file('competencies')) {
            $file->storeAs(
                'public/Ciclos',
                $id.'.txt'
            );
        }
        return $this->redirect();
    }

    /**
     * Elimina un cicle amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->findCicloOrFail($id));
        return parent::destroy($id);
    }

}
