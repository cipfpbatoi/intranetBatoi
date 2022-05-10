<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Lote;
use Illuminate\Database\Eloquent\Builder;
use Intranet\Botones\BotonImg;


/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class ArticuloLoteController extends IntranetController
{
    use traitImprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'ArticuloLote';
    protected $parametresVista = ['modal' => ['explicacion']];


    protected $gridFields = ['id','lote_id', 'descripcion', 'marca', 'modelo', 'unidades'];


    public function search()
    {
        $lotes = hazArray(Lote::where('departamento_id',AuthUser()->departamento)->orWhere('departamento_id',null)->get(),'registre','registre');
        return ArticuloLote::whereHas('materiales', function (Builder $query) use ($lotes) {
            $query->where('espacio', 'like', 'INVENT')
            ->whereIn('lote_id',$lotes);
        })->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('articuloLote.show'));
    }


}
