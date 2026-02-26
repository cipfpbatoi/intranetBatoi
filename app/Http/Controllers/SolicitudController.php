<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;


use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Entities\Solicitud;
use Intranet\Http\Requests\SolicitudRequest;
use Intranet\Http\Traits\Core\DropZone;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Presentation\Crud\SolicitudCrudSchema;


/**
 * Class ExpedienteController
 * @package Intranet\Http\Controllers
 */
class SolicitudController extends ModalController
{

    use DropZone;

    /**
     * @var array
     */
    protected $gridFields = SolicitudCrudSchema::GRID_FIELDS;
    /**
     * @var string
     */
    protected $model = 'Solicitud';
    protected $profile = false;



    public function store(SolicitudRequest $request)
    {
        $this->authorize('create', Solicitud::class);
        $request->merge(['idProfesor' => AuthUser()->dni]);
        $id = $this->persist($request);
        return $this->confirm($id);
    }


    public function update(SolicitudRequest $request, $id)
    {
        $this->authorize('update', Solicitud::findOrFail((int) $id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    public function confirm($id){
        $solicitud = Solicitud::findOrFail($id);
        $this->authorize('view', $solicitud);
        if ($solicitud->estado == 0 && $solicitud->idOrientador) {
            return ConfirmAndSend::render($this->model, $id,'Enviar a '.$solicitud->Orientador->FullName);
        } else {
            return $this->redirect();
        }
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'],['show']);
        $this->panel->setBoton('grid', new BotonImg('solicitud.delete', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('solicitud.edit', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('solicitud.init', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('solicitud.link', ['where' => ['estado', '==', '3']]));
    }



    //inicializat a init (normalment 1)

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function init($id)
    {
        $expediente = Solicitud::findOrFail((int) $id);
        $this->authorize('update', $expediente);
        $expediente->estado = 1;
        $expediente->save();
        app(NotificationService::class)->send($expediente->idOrientador, "T'he remes un cas per al seu estudi", '#', AuthUser()->fullName);

        return back();
    }


    protected function createWithDefaultValues($default = [])
    {
        return new Solicitud(['idProfesor'=>AuthUser()->dni]);
    }


    /*
    * show($id) return vista
    * busca en model de dades i el mostra amb vista show
    */

    public function show($id)
    {
        $elemento = Solicitud::findOrFail($id);
        $this->authorize('view', $elemento);
        $modelo = $this->model;
        return view('solicitud.show', compact('elemento', 'modelo'));
    }

    /**
     * Elimina una sol·licitud amb autorització explícita.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $this->authorize('delete', Solicitud::findOrFail((int) $id));
        return parent::destroy($id);
    }

}
