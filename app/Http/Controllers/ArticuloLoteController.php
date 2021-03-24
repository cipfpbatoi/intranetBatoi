<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\ArticuloLote;
use Illuminate\Database\Eloquent\Builder;
use Intranet\Botones\BotonImg;


/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class ArticuloLoteController extends IntranetController
{

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
        return ArticuloLote::whereHas('materiales', function (Builder $query) {
            $query->where('espacio', 'like', 'INVENT');
        })->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('articuloLote.show'));
    }
}
