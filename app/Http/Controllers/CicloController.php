<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

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
        $ciclo = $this->findModelOrFail(Ciclo::class, $id, 'Cicle no trobat', ['ciclo_id' => $id]);
        $this->authorize('update', $ciclo);
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
        $ciclo = $this->findModelOrFail(Ciclo::class, $id, 'Cicle no trobat', ['ciclo_id' => $id]);
        $this->authorize('delete', $ciclo);
        return parent::destroy($id);
    }

}
