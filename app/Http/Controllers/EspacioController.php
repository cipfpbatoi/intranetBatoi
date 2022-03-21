<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Espacio;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Http\Requests\EspacioRequest;

/**
 * Class EspacioController
 * @package Intranet\Http\Controllers
 */
class EspacioController extends ModalController
{
    use traitImprimir;

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


    public function store(EspacioRequest $request)
    {
        $new = new Espacio();
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(EspacioRequest $request, $id)
    {
        $espacio = Espacio::findOrFail($id)->fillAll($request);
        return $this->redirect();
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
        $this->panel->setBoton('grid', new BotonImg('material.detalle'));
        $this->panel->setBoton('grid', new BotonImg('espacio.edit', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid', new BotonImg('espacio.delete', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid', new BotonImg('espacio.barcode', ['class'=>'QR','img'=>'fa-barcode','roles' => config(self::DIRECCION)]));

    }

    public function barcode($id,$posicion=1){
        $espacio = Espacio::findOrFail($id);
        return $this->hazPdf('pdf.inventario.lote',$espacio->Materiales,$posicion,'portrait',[210,297],5)->stream();
    }

    public function carnet($grupo)
    {
        return $this->hazPdf('pdf.carnet', $this->alumnos($grupo), [Date::now()->format('Y'), 'Alumnat - Student'], 'portrait', [85.6, 53.98])->stream();
    }
}
