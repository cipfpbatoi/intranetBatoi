<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Entities\Solicitud;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\SolicitudRequest;
use Intranet\Http\Traits\Core\DropZone;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Presentation\Crud\SolicitudCrudSchema;


/**
 * Class SolicitudController
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

    /**
     * @param SolicitudRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(SolicitudRequest $request, $id)
    {
        $solicitud = $this->findModelOrFail(
            Solicitud::class,
            $id,
            'Sol·licitud no trobada',
            ['solicitud_id' => $id]
        );
        $this->authorize('update', $solicitud);
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirm($id){
        $solicitud = $this->findModelOrFail(
            Solicitud::class,
            $id,
            'Sol·licitud no trobada',
            ['solicitud_id' => $id]
        );
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
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function init($id)
    {
        $expediente = $this->findModelOrFail(
            Solicitud::class,
            $id,
            'Sol·licitud no trobada',
            ['solicitud_id' => $id]
        );
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

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $elemento = $this->findModelOrFail(
            Solicitud::class,
            $id,
            'Sol·licitud no trobada',
            ['solicitud_id' => $id]
        );
        $this->authorize('view', $elemento);
        $modelo = $this->model;
        return view('solicitud.show', compact('elemento', 'modelo'));
    }

    /**
     * Elimina una sol·licitud amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $solicitud = $this->findModelOrFail(
            Solicitud::class,
            $id,
            'Sol·licitud no trobada',
            ['solicitud_id' => $id]
        );
        $this->authorize('delete', $solicitud);
        return parent::destroy($id);
    }

}
