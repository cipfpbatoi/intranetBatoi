<?php
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Intranet\Http\Requests\ArticuloRequest;
use Intranet\Entities\Articulo;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Class ArticuloController
 * @package Intranet\Http\Controllers
 */
class ArticuloController extends ModalController
{


    /**
     * @var string
     */
    protected $model = 'Articulo';
    protected $modal = true;
    protected $gridFields = ['id', 'descripcion','miniatura'];


    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['show','edit','delete','detalle']);
    }


    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detalle($id)
    {
        try {
            $article = Articulo::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Article no trobat', ['articulo_id' => $id], $e);
        }
        $this->authorize('view', $article);
        return redirect()->route('material.espacio', ['espacio' => $article->descripcion]);
    }

    public function store(ArticuloRequest $request)
    {
        $this->authorize('create', Articulo::class);
        $this->persist($request);
        return $this->redirect();
    }

    /**
     * @param ArticuloRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ArticuloRequest $request, $id)
    {
        try {
            $this->authorize('update', Articulo::findOrFail((int) $id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Article no trobat', ['articulo_id' => $id], $e);
        }
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un article amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        try {
            $this->authorize('delete', Articulo::findOrFail((int) $id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Article no trobat', ['articulo_id' => $id], $e);
        }
        return parent::destroy($id);
    }

    protected function borrarFichero($fichero){
        if (Storage::disk('public')->exists($fichero)) {
            Storage::disk('public')->delete($fichero);
        }
    }


}
