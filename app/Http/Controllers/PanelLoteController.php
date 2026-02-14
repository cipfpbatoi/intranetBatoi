<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Lote;
use Intranet\Http\Traits\Core\Imprimir;

/**
 * Class LoteController
 * @package Intranet\Http\Controllers
 */
class PanelLoteController extends ModalController
{

    use Imprimir;

    /**
     * @var string
     */
    protected $model = 'Lote';
    /**
     * @var array
     */
    protected $vista = 'lote.departamento';


    protected $gridFields = [ 'registre', 'proveedor','factura','estat','fechaAlta'];

    protected function search()
    {
        return Lote::where('departamento_id',AuthUser()->departamento)->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('lote.barcode',['class'=>'QR','img'=>'fa-barcode','where'=>['estado','==',3]]));
     }

    protected function barcode($id,$posicion=1){
        return $this->hazPdf('pdf.inventario.lote', Lote::findOrFail($id)->Materiales, $posicion, 'portrait',[210,297],5)->stream();
    }

}
