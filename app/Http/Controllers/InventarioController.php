<?php
namespace Intranet\Http\Controllers;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class InventarioController extends IntranetController
{

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

    }


}
