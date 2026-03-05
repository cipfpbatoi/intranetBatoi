<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Articulo;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Lote;
use Intranet\Entities\Material;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\LoteRequest;
use Intranet\Http\Traits\Core\Imprimir;

/**
 * Class LoteController
 * @package Intranet\Http\Controllers
 */
class LoteController extends ModalController
{

    use Imprimir;

    /**
     * @var string
     */
    protected $model = 'Lote';
    /**
     * @var array
     */
    protected $vista = 'lote.index';

    protected $gridFields = [ 'registre', 'proveedor','factura','procedencia', 'estado','fechaAlta','departamento'];

    protected function search()
    {
        return Lote::query()
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



    public function store(LoteRequest $request)
    {
        $this->authorize('create', Lote::class);
        $this->persist($request);
        return $this->redirect();
    }

    /**
     * @param LoteRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(LoteRequest $request, $id)
    {
        $this->authorize('update', $this->findModelOrFail(Lote::class, (string) $id, 'Lot no trobat', ['lote_id' => $id]));
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un lot amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->findModelOrFail(Lote::class, (string) $id, 'Lot no trobat', ['lote_id' => $id]));
        return parent::destroy($id);
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('direccion.lote.create', ['text'=>'Nova Factura','roles' => [config('roles.rol.direccion'), config('roles.rol.administrador')]]));
    }

    /**
     * @param int|string $id
     * @param int $posicion
     * @throws NotFoundDomainException
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function print($id,$posicion=1){
        $lote = $this->findModelOrFail(Lote::class, (string) $id, 'Lot no trobat', ['lote_id' => $id]);
        $this->authorize('update', $lote);
        return $this->hazPdf('pdf.inventario.lote', $lote->Materiales, $posicion, 'portrait',[210,297],5)->stream();
    }

    /**
     * @param int|string $lote
     * @throws NotFoundDomainException
     * @return \Illuminate\View\View
     */
    protected function capture($lote){
        $this->authorize('update', $this->findModelOrFail(Lote::class, (string) $lote, 'Lot no trobat', ['lote_id' => $lote]));
        $materiales = Material::whereNotNull('fechaultimoinventario')->where('inventariable',0)->get();
        return view('lote.inventario',compact('lote','materiales'));
    }

    /**
     * @param int|string $lote
     * @param Request $request
     * @throws NotFoundDomainException
     * @return void
     */
    protected function postCapture($lote,Request $request){
       $this->authorize('update', $this->findModelOrFail(Lote::class, (string) $lote, 'Lot no trobat', ['lote_id' => $lote]));
       foreach ($request->except('_token') as $key => $value){
           $material = Material::find($key);
           if (!$material) {
               throw new NotFoundDomainException('Material no trobat', ['material_id' => $key]);
           }
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
