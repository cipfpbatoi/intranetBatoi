<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\QueryException;
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
        try {
            $this->persist($request);
        } catch (QueryException $e) {
            if ($this->isDuplicateAulaQueryException($e)) {
                return $this->duplicateAulaResponse();
            }
            throw $e;
        }
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
        $espacio = $this->findModelOrFail(Espacio::class, $id, 'Espai no trobat', ['espacio_id' => $id]);
        $this->authorize('update', $espacio);
        try {
            $this->persist($request, $id);
        } catch (QueryException $e) {
            if ($this->isDuplicateAulaQueryException($e)) {
                return $this->duplicateAulaResponse();
            }
            throw $e;
        }
        return $this->redirect();
    }

    /**
     * Detecta violació d'unicitat de la PK `espacios.aula`.
     *
     * @param QueryException $e
     * @return bool
     */
    private function isDuplicateAulaQueryException(QueryException $e): bool
    {
        $message = strtolower((string) $e->getMessage());

        return (string) $e->getCode() === '23000'
            && str_contains($message, 'duplicate entry')
            && str_contains($message, 'espacios.primary');
    }

    /**
     * Genera resposta de validació per aula duplicada.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function duplicateAulaResponse()
    {
        return redirect()->back()
            ->withInput()
            ->withErrors(['aula' => 'L\'aula ja existeix.']);
    }

    /**
     * Elimina un espai amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $espacio = $this->findModelOrFail(Espacio::class, $id, 'Espai no trobat', ['espacio_id' => $id]);
        $this->authorize('delete', $espacio);
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
        $espacio = $this->findModelOrFail(Espacio::class, $id, 'Espai no trobat', ['espacio_id' => $id]);
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
