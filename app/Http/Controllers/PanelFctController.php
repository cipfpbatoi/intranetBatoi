<?php

namespace Intranet\Http\Controllers;


use DB;
use Intranet\Entities\Fct;
use Intranet\Botones\BotonIcon;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonImg;



/**
 * Class FctController
 * @package Intranet\Http\Controllers
 */
class PanelFctController extends IntranetController
{

    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    const ROLES_ROL_PRACTICAS = 'roles.rol.practicas';


    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Fct';
    /**
     * @var array
     */
    protected $gridFields = ['Centro','Contacto','Lalumnes','Nalumnes','sendCorreo'];
    /**
     * @var
     */
    protected $grupo;
    /**
     * @var array
     */
    protected $vista = ['show' => 'fct'];
    protected $parametresVista = ['modal' => ['contacto',  'seleccion']];



    /**
     * @var bool
     */
    protected $modal = false;

    use traitPanel;


    /**
     * @return mixed
     */
    public function index()
    {
        $todos = $this->search();
        $this->crea_pestanas(
            [ 0 => 'Contactes' ],
            "profile.fct",
            0,
            0,
           'correoInstructor'
        );
        $this->iniBotones();
        Session::put('redirect', 'PanelFctController@index');
        return $this->grid($todos);
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton(
            'fct',
            new BotonIcon(
                'fct.telefonico',
                [
                    'roles' => config(self::ROLES_ROL_PRACTICAS),
                    'class' => 'btn-primary informe telefonico',
                    'text' => '',
                    'title' => 'Contacte telefònic',
                    'icon' => 'fa-phone'
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "colaboracion.inicioEmpresa",
                [
                    'class' => 'btn-primary selecciona',
                    'icon' => 'fa fa-flag-o',
                    'data-url' => '/api/documentacionFCT/inicioEmpresa'
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "colaboracion.inicioAlumno",
                [
                    'class' => 'btn-primary selecciona',
                    'icon' => 'fa fa-unlock',
                    'data-url' => '/api/documentacionFCT/inicioAlumno'
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "colaboracion.seguimiento",
                [
                    'class' => 'btn-primary selecciona',
                    'icon' => 'fa fa-envelope',
                    'data-url' => '/api/documentacionFCT/seguimiento'
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "colaboracion.visitaEmpresa",
                [
                    'class' => 'btn-info selecciona',
                    'icon' => 'fa fa-bullhorn',
                    'data-url' => '/api/documentacionFCT/visitaEmpresa'
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "colaboracion.citarAlumnos",
                [
                    'class' => 'btn-info selecciona',
                    'icon' => 'fa fa-bullhorn',
                    'data-url' => '/api/documentacionFCT/citarAlumnos'
                ]
            )
        );
        $this->panel->setBothBoton(
            'fct.pdf',
                [
                    'roles' => config(self::ROLES_ROL_PRACTICAS),
                    'class' => '',
                    'text' => 'Cert.Inst.',
                    'title' => 'Certificat Instructor',
                    'icon' => 'fa-file-pdf-o'
                ]
        );
        $this->panel->setBothBoton(
            'fct.colaborador',
            [
                'roles' => config(self::ROLES_ROL_PRACTICAS),
                'class' => '',
                'text' => 'Cert.Col.',
                'title' => 'Certificat Col·laboradors',
                'icon' => 'fa-file-text'
            ]
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "alumnofct",
                [
                    'class' => 'btn-link',
                    'roles' => config(self::ROLES_ROL_TUTOR)
                ]
            )
        );
    }


    /**
     * @return mixed
     */
    public function search()
    {
        return Fct::misFcts()->esFct()->get();
    }



}
