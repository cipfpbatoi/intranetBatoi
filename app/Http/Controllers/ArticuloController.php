<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Articulo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class ArticuloController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Articulo';
    protected $parametresVista = ['modal' => ['explicacion']];


    protected $gridFields = ['id','lote_registre', 'descripcion', 'marca', 'modelo', 'unidades'];


    public function search()
    {
        return Articulo::whereHas('materiales', function (Builder $query) {
            $query->where('espacio', 'like', 'INVENT');
        })->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('articulo.show'));
    }
}
