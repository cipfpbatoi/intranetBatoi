<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Solicitud;


/**
 * Class PanelSolicitudOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelSolicitudOrientacionController extends ModalController
{


    /**
     * @var array
     */
    protected $gridFields = ['id', 'nomAlum', 'fecha', 'motiu','situacion'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Solicitud';
    /**
     * @var string
     */
    protected $orden = 'fecha';


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['show']);
        $this->panel->setBoton('grid', new BotonImg('solicitud.active', ['where' => ['estado', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('solicitud.link', ['where' => ['estado', '>', '1']]));
        $this->panel->setBoton('grid', new BotonImg('solicitud.active', ['img'=>'fa-flag-o', 'where' => ['estado', '==', '2']]));
    }

    public function active($id)
    {
        $elemento = Solicitud::findOrFail($id);
        if ($elemento->estado == 1){
            $elemento->estado = 2;
            $elemento->save();
            avisa($elemento->idProfesor,"El departament d'orientació comença a tramitar la teua sol·licitud en nom de ".$elemento->Alumno->fullName,'#',$elemento->Orientador->fullName);
        } else {
            $elemento->estado = 3;
            $elemento->save();
            avisa($elemento->idProfesor,"El departament d'orientació ha finalitzat la teua sol·licitud en nom de ".$elemento->Alumno->fullName,'#',$elemento->Orientador->fullName);
        }
        return back();
    }



    public function search()
    {
        return Solicitud::where('idOrientador',AuthUser()->dni)->where('estado','>',0)->get();
    }

    

}
