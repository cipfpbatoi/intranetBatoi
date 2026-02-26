<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Http\Requests\PPollRequest;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Poll\Option;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Support\Facades\Hash;
use Intranet\Entities\Poll\PPoll;

class PPollController extends ModalController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades

    protected $model = 'PPoll';
    protected $gridFields = [ 'id','title','what'];
    
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("ppoll.create",inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('ppoll.edit',inRol('qualitat')));
        $this->panel->setBoton('grid', new BotonImg('ppoll.delete',array_merge(inRol('qualitat'),['where' => ['remains','==','0']])));
        $this->panel->setBoton('grid', new BotonImg('ppoll.show',array_merge(['img'=>'fa-plus'],inRol('qualitat'))));
    }

    public function show($id)
    {
        $elemento = PPoll::findOrFail($id);
        $this->authorize('view', $elemento);
        $modelo = $this->model;
        return view('poll.masterslave', compact('elemento','modelo'));
    }

    public function store(PPollRequest $request)
    {
        $this->authorize('create', PPoll::class);
        $this->persist($request);
        return $this->redirect();
    }

    public function update(PPollRequest $request, $id)
    {
        $this->authorize('update', PPoll::findOrFail((int) $id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina una plantilla de poll amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', PPoll::findOrFail((int) $id));
        return parent::destroy($id);
    }
}
