<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Falta;
use Intranet\Http\Traits\Core\Panel;

/**
 * Class PanelFaltaController
 * @package Intranet\Http\Controllers
 */
class PanelFaltaController extends ModalController
{
    use Panel;

    const ROLES_ROL_DIRECCION = 'roles.rol.direccion';

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
    protected $gridFields = ['id', 'nombre', 'desde', 'hasta', 'motivo', 'situacion'];


    /**
     * @var bool
     */
    protected $notFollow = true;
    /**
     * @var array
     */
    protected $parametresVista = ['modal' => ['explicacion','loading','ItacaPassword']];
    protected $formFields = [
        'idProfesor' => ['type' => 'select'],
        'estado' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'baja' => ['type' => 'checkbox'],
        'dia_completo' => ['type' => 'checkbox'],
        'hora_ini' => ['type' => 'time'],
        'hora_fin' => ['type' => 'time'],
        'motivos' => ['type' => 'select'],
        'observaciones' => ['type' => 'text'],
        'fichero' => ['type' => 'file'],

    ];

    protected function search()
    {
       return(Falta::orderBy('desde')->get());
       // return Falta::where('dia_completo', 1)->whereMonth('hasta', '=', 2)->get();
    }


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "direccion.itaca.birret",
                ['class' => 'btn-info convalidacion', 'roles' => config(self::ROLES_ROL_DIRECCION)]
            ));
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                "$this->model.resolve",
                [
                    'class' => 'btn-success authorize',
                    'where' => ['estado', '>', '0', 'estado', '<', '3']
                ],
                true
            )
        );
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                "$this->model.refuse",
                [
                    'class' => 'btn-danger refuse',
                    'where' => ['estado', '>', '0', 'estado', '<', '4']
                ],
                true
            )
        );
        $this->panel->setBoton('profile', new BotonIcon("$this->model.alta", ['class' => 'btn-success alta', 'where' => ['estado', '==', '5']], true));
        $this->panel->setBoton('grid', new BotonImg('falta.delete', ['where' => ['estado', '<', '4']]));
        $this->panel->setBoton('grid', new BotonImg('falta.edit', ['where' => ['estado', '<', '4']]));
        $this->panel->setBoton('grid', new BotonImg('falta.notification', ['where' => ['estado', '>', '0', 'hasta', 'posterior', Ayer()]]));
        $this->panel->setBothBoton('falta.document', ['where' => ['fichero', '!=', '']]);
        $this->panel->setBothBoton('falta.gestor', ['img' => 'fa-eye', 'where'=>['idDocumento','!=',null]]);
        
    }
}
