<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Falta\FaltaService;
use Intranet\Entities\Falta;
use Intranet\Http\Controllers\Core\ModalController;
use Intranet\Http\Requests\FaltaRequest;
use Illuminate\Http\Request;

use Intranet\UI\Botones\BotonImg;
use Intranet\Presentation\Crud\FaltaCrudSchema;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\Notifications\ConfirmAndSend;
use Illuminate\Support\Carbon;


/**
 * Class FaltaController
 * @package Intranet\Http\Controllers
 */
class FaltaController extends ModalController
{
    private ?FaltaService $faltaService = null;

    use Imprimir, Autorizacion;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Falta';
    /**
     * @var array
     */
    protected $gridFields = FaltaCrudSchema::GRID_FIELDS;
    public function __construct(?FaltaService $faltaService = null)
    {
        parent::__construct();
        $this->faltaService = $faltaService;
    }

    private function faltas(): FaltaService
    {
        if ($this->faltaService === null) {
            $this->faltaService = app(FaltaService::class);
        }

        return $this->faltaService;
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('falta.delete', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.edit', ['where' => ['estado', '<', '3']]));
        $this->panel->setBoton('grid', new BotonImg('falta.init', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.document', ['where' => ['fichero', '!=', '']]));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Falta::class);
        $this->validate($request, (new FaltaRequest())->rules());
        $id = $this->faltas()->create($request);

        if (!$request->boolean('baja') && UserisAllow(config('roles.rol.direccion'))) {
            $this->faltas()->init($id);
        } elseif (!$request->boolean('baja')) {
            return ConfirmAndSend::render($this->model, $id);
        }

        return $this->redirect();
    }

    

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', $this->findFaltaOrFail((int) $id));
        $this->validate($request, (new FaltaRequest())->rules());
        $this->faltas()->update($id, $request);
        return $this->redirect();
    }

    protected function createWithDefaultValues($default = [])
    {
        $data = new Carbon('today');
        return new Falta(['desde'=>$data,'hasta'=>$data,'idProfesor'=>AuthUser()->dni]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function init($id)
    {
        $this->authorize('update', $this->findFaltaOrFail((int) $id));
        $this->faltas()->init($id);
        return $this->redirect();
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alta($id)
    {
        $this->authorize('update', $this->findFaltaOrFail((int) $id));
        $elemento = $this->faltas()->alta($id);
        return back()->with('pestana', $elemento->estado);
    }

    /**
     * Recupera la falta per aplicar autorització explícita.
     */
    private function findFaltaOrFail(int $id): Falta
    {
        return Falta::findOrFail($id);
    }

    /**
     * Mostra el detall d'una falta.
     *
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $elemento = $this->findFaltaOrFail((int) $id);
        $this->authorize('view', $elemento);
        $modelo = $this->model;
        return view('intranet.show', compact('elemento', 'modelo'));
    }
}
