<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Inventario;
use Intranet\Services\FormBuilder;
use Jenssegers\Date\Date;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class InventarioController extends IntranetController
{
    use traitImprimir;
    /**
     * @var string
     */
    protected $model = 'Inventario';
    /**
     * @var array
     */
    protected $vista = ['index' => 'material' ];
    /**
     * @var array
     */
    protected $gridFields = ['id', 'descripcion', 'Estado', 'espacio'];
    /**
     * @var array
     */

    protected $formFields =  [
        'nserieprov' => ['type' => 'text'],
        'descripcion' => ['type' => 'text'],
        'marca' => ['type' => 'text'],
        'modelo' => ['type' => 'text'],
        'ISBN' => ['type' => 'text'],
        'espacio' => ['disabled' => 'disabled'],
        'procedencia' => ['disabled' => 'disabled'],
        'proveedor' => ['disabled' => 'disabled'],
        'inventariable' => ['type' => 'checkbox'],
        'estado' => ['disabled' => 'disabled'],
        'articulo_lote_id' => ['disabled' => 'disabled'],
        'unidades' => ['type' => 'hidden']
    ];


    public function search(){
        // empty
    }

    protected function qr($id){
        $material = Inventario::findOrFail($id);
        return $this->hazPdf('pdf.inventario.qr', Inventario::findOrFail($id), [Date::now()->format('Y'), 'Alumne - Student'], 'portrait', [85.6, 53.98])->stream();
    }

    public function edit($id){
        $material = Inventario::findOrFail($id);
        if ($material->espacio == 'INVENT'){
            $formulario = new FormBuilder($material,[
                'descripcion' => ['disabled' => 'disabled'],
                'marca' => ['disabled' => 'disabled'],
                'modelo' => ['disabled' => 'disabled'],
                'nserieprov' => ['type' => 'text'],
                'espacio' => ['type' => 'select']]);
            $modelo =  $this->model;
            return view('intranet.edit',compact('formulario','modelo'));
        }
    }


}
