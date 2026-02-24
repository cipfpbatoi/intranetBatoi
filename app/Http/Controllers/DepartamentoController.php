<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Departamento;
use Intranet\Http\Requests\DepartamentoRequest;

/**
 * Class CicloController
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

    public function update(DepartamentoRequest $request, $id)
    {
        $this->authorize('update', Departamento::findOrFail((int) $id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un departament amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', Departamento::findOrFail((int) $id));
        return parent::destroy($id);
    }

    protected function search()
    {
        return  Departamento::all(); // carrega totes les dades de un model
    }

}
