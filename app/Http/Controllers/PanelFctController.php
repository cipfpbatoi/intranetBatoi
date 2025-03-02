<?php

namespace Intranet\Http\Controllers;


use DB;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Entities\Fct;
use Intranet\Http\Traits\Panel;


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
    protected $gridFields = [];
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

    use Panel;


    /**
     * @return mixed
     */
    public function index()
    {
        $todos = $this->search();
        $this->setTabs(
            [ 0 => 'Actius', 1 => 'Finalizats'],
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
                "comision.create",
                [
                    'class' => 'btn-primary',
                    'text'=> 'Comissió Servei',
                    'icon' => 'fa fa-car',
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
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "alumnofct",
                [
                    'class' => 'btn-dark',
                    'text' => 'Llistat',
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
        $fcts = Fct::where(function($query) {
            $query->esFct() // Suposem que aquest mètode filtra per FCT
            ->orWhere->esDual(); // Suposem que aquest mètode filtra per Dual
        })
            ->where(function($query) {
                $query->misFcts() // Suposem que aquest mètode filtra FCTs pertanyents a l'usuari autenticat
                ->orWhere('cotutor', authUser()->dni); // O on l'usuari autenticat és cotutor
            })
            ->has('AlFct') // Suposem que això filtra FCTs que tenen almenys un AlFct associat
            ->get();
        return $fcts->sortBy('centro');
    }



}
