<?php
namespace Intranet\Http\Controllers;


use Intranet\Botones\BotonImg;
use Intranet\Http\Requests\ArticuloRequest;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class ArticuloController extends LaravelController
{

    use traitCrudvII;
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Articulo';
    protected $modal = true;
    protected $vista = ['show'=>'articulo.show'];
    protected $gridFields = ['id', 'descripcion','miniatura'];


    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('articulo.show',['class'=>'mostra']));
        $this->panel->setBoton('grid', new BotonImg('articulo.edit'));
        $this->panel->setBoton('grid', new BotonImg('articulo.delete'));
    }

    public function store(ArticuloRequest $request)
    {
        $this->realStore($request);
        return $this->redirect();
    }

    public function update(ArticuloRequest $request, $id)
    {
        $this->realStore($request, $id);
        return $this->redirect();
    }



}
