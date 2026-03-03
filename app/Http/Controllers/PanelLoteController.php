<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Lote;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Traits\Core\Imprimir;

/**
 * Class PanelLoteController
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
        return Lote::query()
            ->where('departamento_id', AuthUser()->departamento)
            ->with('Departamento')
            ->withCount([
                'ArticuloLote',
                'Materiales',
                'Materiales as materiales_invent_count' => static function ($query) {
                    $query->where('espacio', 'INVENT');
                },
            ])
            ->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('lote.barcode',['class'=>'QR','img'=>'fa-barcode','where'=>['estado','==',3]]));
     }

    /**
     * @param int|string $id
     * @param int $posicion
     * @throws NotFoundDomainException
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function barcode($id,$posicion=1){
        $lote = $this->findModelOrFail(Lote::class, $id, 'Lot no trobat', ['lote_id' => $id]);
        return $this->hazPdf('pdf.inventario.lote', $lote->Materiales, $posicion, 'portrait',[210,297],5)->stream();
    }

}
