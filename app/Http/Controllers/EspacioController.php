<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Espacio;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\EspacioRequest;
use Intranet\Http\Traits\Core\Imprimir;

/**
 * Class EspacioController
 * @package Intranet\Http\Controllers
 */
class EspacioController extends ModalController
{
    use Imprimir;

    const DIRECCION = 'roles.rol.direccion';

    /**
     * @var string
     */
    protected $model = 'Espacio';
    /**
     * @var array
     */
    protected $gridFields = ['Xdepartamento', 'aula', 'descripcion', 'gMati', 'gVesprada'];

    protected $formFields = [
        'aula' => ['type' => 'text'],
        'descripcion' => ['type' => 'text'],
        'idDepartamento' => ['type' => 'select'],
        'gMati' => ['type' => 'select'],
        'gVesprada' => ['type' => 'select'],
        'reservable' => ['type' => 'checkbox'],
    ];

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return Espacio
     */
    private function findEspacioOrFail($id): Espacio
    {
        try {
            return Espacio::findOrFail((string) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Espai no trobat', ['espacio_id' => $id]);
        }
    }

    public function search()
    {
        if (esRol(authUser()->rol, config(self::DIRECCION))) {
            return Espacio::all();
        } else {
            return Espacio::where('idDepartamento', AuthUser()->departamento)->get();
        }
    }

    public function store(EspacioRequest $request)
    {
        $this->authorize('create', Espacio::class);
        $this->persist($request);
        return $this->redirect();
    }

    /**
     * @param EspacioRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(EspacioRequest $request, $id)
    {
        $this->authorize('update', $this->findEspacioOrFail($id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un espai amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->findEspacioOrFail($id));
        return parent::destroy($id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detalle($id)
    {
        return redirect()->route('material.espacio', ['espacio' => $id]);
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('espacio.create', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid', new BotonImg('inventario.detalle'));
        $this->panel->setBoton('grid', new BotonImg('espacio.edit', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid', new BotonImg('espacio.delete', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'espacio.barcode',
                ['class'=>'QR','img'=>'fa-barcode','roles' => config(self::DIRECCION)]
            )
        );

    }

    /**
     * @param int|string $id
     * @param int $posicion
     * @throws NotFoundDomainException
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function barcode($id, $posicion=1)
    {
        $espacio = $this->findEspacioOrFail($id);
        $this->authorize('printBarcode', $espacio);
        return $this->hazPdf(
            'pdf.inventario.lote',
            $espacio->Materiales,
            $posicion,
            'portrait',
            [210, 297],
            5
        )->stream();
    }


}
