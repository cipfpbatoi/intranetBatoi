<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Http\Requests\LoteRequest;
use Intranet\Entities\Lote;
use Jenssegers\Date\Date;

/**
 * Class LoteController
 * @package Intranet\Http\Controllers
 */
class LoteController extends ModalController
{

    use traitImprimir;

    /**
     * @var string
     */
    protected $model = 'Lote';
    /**
     * @var array
     */
    protected $vista = 'lote.index';


    protected $gridFields = [ 'registre', 'proveedor','procedencia', 'estado','fechaAlta'];



    public function store(LoteRequest $request)
    {
        $new = new Lote();
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(LoteRequest $request, $id)
    {
        Lote::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('direccion.lote.create', ['text'=>'Nova Factura','roles' => config('roles.rol.direccion')]));
    }

    protected function print($id){
        $lote = Lote::findOrFail($id);
        return $this->hazPdf('pdf.inventario.lote', Lote::findOrFail($id)->Materiales, [Date::now()->format('Y')], 'portrait')->stream();
    }

}
