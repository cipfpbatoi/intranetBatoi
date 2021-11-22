<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Botones\BotonBasico;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Articulo;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Material;
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


    protected $gridFields = [ 'registre', 'proveedor','factura','procedencia', 'estado','fechaAlta'];



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
        return $this->hazPdf('pdf.inventario.lote', Lote::findOrFail($id)->Materiales, [Date::now()->format('Y')], 'portrait','a4','1.9cm')->stream();
    }

    protected function capture($lote){
        $materiales = Material::whereNotNull('fechaultimoinventario')->get();
        return view('lote.inventario',compact('lote','materiales'));
    }

    protected function postCapture($lote,Request $request){
       foreach ($request->except('_token') as $key => $value){
           $material = Material::find($key);
           if (!$value) {
               $value = $material->descripcion;
           }
           DB::transaction(function () use ($material,$value,$lote){
               $articulo = Articulo::where('descripcion',$value)->first();
               if (!$articulo){
                   $articulo = new Articulo(['descripcion'=>$value]);
                   $articulo->save();
               }
               $articulo_lote = new ArticuloLote(['lote_id'=>$lote,'articulo_id'=>$articulo->id,'marca'=>$material->marca,'modelo'=>$material->modelo,'unidades'=>$material->unidades]);
               $articulo_lote->save();
               for ($i=0; $i<$material->unidades;$i++){
                 $new = $material->replicate();
                 $new->unidades = 1;
                 $new->inventariable = 1;
                 $new->fechaultimoinventario = null;
                 $new->articulo_lote_id = $articulo_lote->id;
                 $new->save();
               }
               $material->delete();
           });
       }
    }

}
