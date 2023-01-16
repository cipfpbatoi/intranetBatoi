<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;


/**
 * Class PanelActividadController
 * @package Intranet\Http\Controllers
 */
class PanelActividadController extends BaseController
{

    use traitPanel;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Actividad';
    /**
     * @var array
     */
    protected $gridFields = ['name', 'desde', 'hasta', 'situacion'];
    /**
     * @var array
     */
    protected $parametresVista = ['before' => [] , 'modal' => ['explicacion']];


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBothBoton('actividad.detalle');
        $this->panel->setBoton('grid', new BotonImg('notificacion', ['where'=>['estado','<','3']]));
        $this->panel->setBoton('grid', new BotonImg('actividad.edit', ['where'=>['estado','<','3']]));
        $this->panel->setBoton('grid', new BotonImg('actividad.delete', ['where'=>['estado','<','3']]));
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                "$this->model.unauthorize",
                [
                    'class' => 'btn-danger unauthorize',
                    'where' => ['estado', '==', '3']
                ],
                true
            )
        );
        $this->panel->setBothBoton('actividad.gestor', ['img' => 'fa-archive', 'where'=>['idDocumento','!=',null]]);
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'actividad.pdfVal',
                [
                    'img'=>'fa-file-pdf-o',
                    'where' => ['estado', '==', '4','hasta','anterior',Hoy()]
                ]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'actividad.showVal',
                [
                    'img'=>'fa-eye-slash',
                    'where' => ['estado', '==', '4','hasta','anterior',Hoy()]
                ]
            )
        );

        $this->setAuthBotonera();
    }



}
