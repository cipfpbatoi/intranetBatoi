<?php
namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Espacio;
use Intranet\Entities\Inventario;
use Intranet\Entities\Material;
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

    function barcode(Request $request){
        $materiales = collect();
        $ids = explode(',',$request->ids);
        foreach ($ids as $id){
            $materiales->add(Material::find($id));
        }
        return $this->hazPdf('pdf.inventario.lote',$materiales,$request->posicion,'portrait',[210,297],5)->stream();

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
        } else {
            if ($material->LoteArticulo->Lote->proveedor == 'Inventario'){
                return parent::editdelete($id);
            } else {
                return parent::edit($id);
            }

        }
    }

    /**
     * @param $espacio
     * @return mixed
     */
    public function espacio($espacio)
    {
        if (Espacio::find($espacio)){
            $this->vista = ['index' => 'Espai'];
        } else {
            $this->vista = ['index' => 'Article'];
        }


        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniPestanas();

        return $this->grid($espacio);
    }


}
