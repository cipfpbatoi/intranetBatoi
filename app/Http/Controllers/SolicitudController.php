<?php

namespace Intranet\Http\Controllers;


use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Entities\Solicitud;
use Intranet\Http\Requests\SolicitudRequest;
use Intranet\Http\Traits\DropZone;
use Intranet\Services\Notifications\ConfirmAndSend;


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
    protected $gridFields = ['id', 'nomAlum', 'fecha', 'situacion'];
    /**
     * @var string
     */
    protected $model = 'Solicitud';
    protected $profile = false;



    public function store(SolicitudRequest $request)
    {
        $new = new Solicitud();
        $new->fillAll($request);
        return $this->confirm($new->id);
    }


    public function update(SolicitudRequest $request, $id)
    {
        Solicitud::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    public function confirm($id){
        $solicitud = Solicitud::findOrFail($id);
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
        $expediente = Solicitud::find($id);
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
        $modelo = $this->model;
        return view('solicitud.show', compact('elemento', 'modelo'));
    }

}
