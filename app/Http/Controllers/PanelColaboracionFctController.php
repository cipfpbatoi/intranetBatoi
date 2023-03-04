<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Grupo;
use Intranet\Finders\UniqueFinder;
use Intranet\Componentes\DocumentoFct;
use Intranet\Finders\RequestFinder;
use Intranet\Services\DocumentService;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\DB;

/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class PanelColaboracionFctController extends IntranetController
{
    use traitPanel;

    const ROLES_ROL_PRACTICAS = 'roles.rol.practicas';
    const FCT_EMAILS_REQUEST = 'fctEmails.request';
    /**
     * @var array
     */
    protected $gridFields = [
        'Empresa', 'concierto', 'Localidad', 'puestos', 'Xestado', 'contacto', 'telefono', 'email'
    ];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Colaboracion';

    protected $parametresVista = ['modal' => ['contacto', 'afegirFct', 'seleccion']];


    /**
     * @return mixed
     */
    public function index()
    {
        $todos = $this->search();

        $this->crea_pestanas(
            config('modelos.'.$this->model.'.estados'),
            "profile.".strtolower($this->model),
            3,
            1,
            'situation'
        );
        $this->iniBotones();
        Session::put('redirect', 'PanelColaboracionController@index');
        return $this->grid($todos);
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                'colaboracion.switch',
                [
                    'roles' => config(self::ROLES_ROL_PRACTICAS),
                    'class' => 'btn-warning switch',
                    'icon' => 'fa-user',
                    'where' => ['tutor', '<>', AuthUser()->dni]
                ]
            )
        );
        $this->panel->setBoton(
            'nofct',
            new BotonIcon(
                'colaboracion.unauthorize',
                [
                    'roles' => config(self::ROLES_ROL_PRACTICAS),
                    'class' => 'btn-primary unauthorize estado',
                    'where' => ['tutor', '==', AuthUser()->dni, 'estado', '!=', '1']
                ]
            )
        );
        $this->panel->setBoton(
            'nofct',
            new BotonIcon(
                'colaboracion.resolve',
                [
                    'roles' => config(self::ROLES_ROL_PRACTICAS),
                    'class' => 'btn-success resolve estado',
                    'where' => ['tutor', '==', AuthUser()->dni, 'estado', '!=', '2']
                ]
            )
        );
        $this->panel->setBoton(
            'nofct',
            new BotonIcon(
                'colaboracion.refuse',
                [
                    'roles' => config(self::ROLES_ROL_PRACTICAS),
                    'class' => 'btn-danger refuse estado',
                    'where' => ['tutor', '==', AuthUser()->dni, 'estado', '!=', '3']
                ]
            )
        );
        $this->panel->setBoton(
            'nofct',
            new BotonIcon(
                'colaboracion.book',
                [
                    'roles' => config(self::ROLES_ROL_PRACTICAS),
                    'class' => 'btn-primary informe book',
                    'text' => '',
                    'title' => 'Contacte previ',
                    'icon' => 'fa-book'
                ]
            )
        );
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
            'pendiente',
            new BotonBasico(
                "colaboracion.contacto",
                [
                    'class' => 'btn-primary selecciona',
                    'icon' => 'fa fa-bell-o',
                    'data-url' => '/api/documentacionFCT/contacto'
                ]
            )
        );
        $this->panel->setBoton(
            'colabora',
            new BotonBasico(
                "colaboracion.revision",
                [
                    'class' => 'btn-primary selecciona',
                    'icon' => 'fa fa-check',
                    'data-url' => '/api/documentacionFCT/revision'
                ]
            )
        );
        $this->panel->setBoton(
            'fcts',
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
            'fcts',
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
            'fcts',
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
            'fcts',
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
            'fcts',
            new BotonBasico(
                "colaboracion.citarAlumnos",
                [
                    'class' => 'btn-info selecciona',
                    'icon' => 'fa fa-bullhorn',
                    'data-url' => '/api/documentacionFCT/citarAlumnos'
                ]
            )
        );

    }

    /**
     * @return mixed
     */
    public function search()
    {
        $colaboracions = Colaboracion::with('propietario')
            ->with('Centro')
            ->with('Centro.Empresa')
            ->MiColaboracion()
            ->get();
        if (count($colaboracions)) {
            $this->titulo = ['quien' => $colaboracions->first()->Ciclo->literal];
        }
        return $colaboracions->sortBy('tutor')->sortBy('empresa');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */


    public function showMailbyId($id, $documento)
    {
        $document = new DocumentoFct($documento);
        $parametres = array('id' => $id, 'document' => $document);
        $service = new DocumentService(new UniqueFinder($parametres));

        return $service->render();
    }


    protected function showMailbyRequest(Request $request, $documento)
    {
        $documento = new DocumentoFct($documento);
        $parametres = array('request' => $request, 'document' => $documento);
        $service = new DocumentService(new RequestFinder($parametres));
        return $service->render();
    }

    /**
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana', 1);
        return $this->showEmpresa($empresa);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        parent::store($request);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana', 1);
        return $this->showEmpresa($empresa);
    }

    private function showEmpresa($id)
    {
        return redirect()->action('EmpresaController@show', ['empresa' => $id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy($id)
    {
        $profesor = AuthUser()->dni;
        $elemento = Colaboracion::find($id);
        Session::put('pestana', 1);
        $copia = new Colaboracion();
        $copia->fill($elemento->toArray());
        $copia->idCiclo = Grupo::QTutor($profesor)->get()->count() > 0
            ? Grupo::QTutor($profesor)->first()->idCiclo
            : Grupo::QTutor($profesor, true)->first()->idCiclo;
        $copia->tutor = AuthUser()->FullName;

        // para no generar más de uno por ciclo
        $validator = Validator::make($copia->toArray(), $copia->getRules());
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }


        $copia->save();
        return back();

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $empresa = Colaboracion::find($id)->Centro->Empresa;
        try {
            parent::destroy($id);
        } catch (Exception $exception) {
            Alert::danger("No es pot esborrar perquè hi ha valoracions
             fetes per a eixa col·laboració d'anys anteriors.");
        }

        Session::put('pestana', 1);
        return $this->showEmpresa($empresa);
    }


}
