<?php
namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Intranet\Http\Requests\ArticuloRequest;
use Intranet\Entities\Articulo;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class ArticuloController extends ModalController
{

    use traitFile;

    /**
     * @var string
     */
    protected $model = 'Articulo';
    protected $modal = true;
    protected $gridFields = ['id', 'descripcion','miniatura'];


    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['show','edit','delete']);
    }

    public function store(ArticuloRequest $request)
    {
        $newArt = new Articulo();
        $newArt->fillAll($request);
        return $this->redirect();
    }

    public function update(ArticuloRequest $request, $id)
    {
        Articulo::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    protected function borrarFichero($fichero){
        if (Storage::disk('public')->exists($fichero)) {
            Storage::disk('public')->delete($fichero);
        }
    }


}
