<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Departamento;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\DepartamentoRequest;

/**
 * Class DepartamentoController
 * @package Intranet\Http\Controllers
 */
class DepartamentoController extends ModalController
{
    const ADMINISTRADOR = 'roles.rol.administrador';

    /**
     * @var string
     */
    protected $model = 'Departamento';
    /**
     * @var array
     */
    protected $gridFields = [ 'id','depcurt','literal'];
    protected $formFields= [
        'id' => ['type' => 'text'],
        'cliteral' => ['type' => 'text'],
        'vliteral' => ['type' => 'text'],
        'depcurt' => ['type' => 'text'],
        'didactico' => ['type' => 'checkbox'],
        'idProfesor' => ['type' => 'select']
    ];

    protected function iniBotones()
    {
        $this->panel->setBoton(
            'index',
            new BotonBasico('departamento.create', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('departamento.show', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('departamento.edit', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('departamento.delete', ['roles' => config(self::ADMINISTRADOR)]));
    }

    public function store(DepartamentoRequest $request)
    {
        $this->authorize('create', Departamento::class);
        $this->persist($request);
        return $this->redirect();
    }

    /**
     * @param DepartamentoRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(DepartamentoRequest $request, $id)
    {
        $departamento = $this->findModelOrFail(
            Departamento::class,
            $id,
            'Departament no trobat',
            ['departamento_id' => $id]
        );
        $this->authorize('update', $departamento);
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un departament amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $departamento = $this->findModelOrFail(
            Departamento::class,
            $id,
            'Departament no trobat',
            ['departamento_id' => $id]
        );
        $this->authorize('delete', $departamento);
        return parent::destroy($id);
    }

    protected function search()
    {
        return  Departamento::all(); // carrega totes les dades de un model
    }

}
