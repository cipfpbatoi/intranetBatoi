<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Falta\FaltaService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Presentation\Crud\FaltaCrudSchema;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\Notifications\ConfirmAndSend;
use Jenssegers\Date\Date;


/**
 * Class FaltaController
 * @package Intranet\Http\Controllers
 */
class FaltaController extends IntranetController
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
    /**
     * @var bool
     */
    protected $modal = true;

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
        $this->faltas()->update($id, $request);
        return $this->redirect();
    }

    protected function createWithDefaultValues($default = [])
    {
        $data = new Date('today');
        return new Falta(['desde'=>$data,'hasta'=>$data,'idProfesor'=>AuthUser()->dni]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function init($id)
    {
        $this->faltas()->init($id);
        return $this->redirect();
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alta($id)
    {
        $elemento = $this->faltas()->alta($id);
        return back()->with('pestana', $elemento->estado);
    }
}
