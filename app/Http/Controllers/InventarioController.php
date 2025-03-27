<?php
namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Espacio;
use Intranet\Entities\Inventario;
use Intranet\Entities\Material;
use Intranet\Http\Traits\Imprimir;
use Intranet\Services\FormBuilder;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class InventarioController extends IntranetController
{
    use Imprimir;
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
    protected $gridFields = ['id','articulo', 'descripcion', 'Estado', 'espacio'];
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


    public function barcode(Request $request)
    {
        $materiales = collect();
        $ids = explode(',', $request->ids);
        foreach ($ids as $id) {
            $materiales->add(Material::find($id));
        }
        return $this->hazPdf('pdf.inventario.lote', $materiales, $request->posicion, 'portrait', [210, 297], 5)->stream();

    }


    public function edit($id)
    {
        $material = Inventario::findOrFail($id);
        if (isProfesor()) {
            if ($material->espacio === 'INVENT') {
                $formulario = new FormBuilder($material, [
                    'descripcion' => ['disabled' => 'disabled'],
                    'marca' => ['disabled' => 'disabled'],
                    'modelo' => ['disabled' => 'disabled'],
                    'nserieprov' => ['type' => 'text'],
                    'espacio' => ['type' => 'select']
                ]);
                $modelo = $this->model;
                return view('intranet.edit', compact('formulario', 'modelo'));
            }

            return parent::edit($id);
        }

        return view('inventario.show', compact('material'));
    }

    /**
     * @param $espacio
     * @return mixed
     */
    public function espacio($espacio)
    {
        $this->vista = [
            'index' => Espacio::find($espacio) ? 'Espai' : 'Articulo'
        ];
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniPestanas();

        return $this->grid($espacio);
    }


}
