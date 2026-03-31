<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Colaboracion\ColaboracionService;
use Intranet\Application\Colaboracion\ColaboracionPreasignacionService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Http\Requests\ColaboracionRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Presentation\Crud\ColaboracionCrudSchema;
use Intranet\Http\Traits\Core\Panel;
use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class PanelColaboracionController extends IntranetController
{
    use Panel;

    private ?GrupoService $grupoService = null;
    private ?ColaboracionService $colaboracionService = null;
    private ?ColaboracionPreasignacionService $preasignacionService = null;

    const ROLES_ROL_TUTOR= 'roles.rol.tutor';
    const FCT_EMAILS_REQUEST = 'fctEmails.request';
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Colaboracion';

    protected $parametresVista = ['modal' => ['contacto',  'seleccion']];

    public function __construct(
        ?GrupoService $grupoService = null,
        ?ColaboracionService $colaboracionService = null,
        ?ColaboracionPreasignacionService $preasignacionService = null
    )
    {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->colaboracionService = $colaboracionService;
        $this->preasignacionService = $preasignacionService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function colaboraciones(): ColaboracionService
    {
        if ($this->colaboracionService === null) {
            $this->colaboracionService = app(ColaboracionService::class);
        }

        return $this->colaboracionService;
    }

    private function preasignaciones(): ColaboracionPreasignacionService
    {
        if ($this->preasignacionService === null) {
            $this->preasignacionService = app(ColaboracionPreasignacionService::class);
        }

        return $this->preasignacionService;
    }


    /**
     * @return mixed
     */
    public function index()
    {
        $todos = $this->search();

        $this->setTabs(
            config('modelos.Colaboracion.estados'),
            "profile.colaboracion",
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
                    'roles' => config(self::ROLES_ROL_TUTOR),
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
                    'roles' => config(self::ROLES_ROL_TUTOR),
                    'class' => 'btn-primary unauthorize estado',
                    'where' => [  'estado', '!=', '1']
                ]
            )
        );
        $this->panel->setBoton(
            'nofct',
            new BotonIcon(
                'colaboracion.resolve',
                [
                    'roles' => config(self::ROLES_ROL_TUTOR),
                    'class' => 'btn-success resolve estado',
                    'where' => [  'estado', '!=', '2']
                ]
            )
        );
        $this->panel->setBoton(
            'nofct',
            new BotonIcon(
                'colaboracion.refuse',
                [
                    'roles' => config(self::ROLES_ROL_TUTOR),
                    'class' => 'btn-danger refuse estado',
                    'where' => [  'estado', '!=', '3']
                ]
            )
        );
        $this->panel->setBoton(
            'nofct',
            new BotonIcon(
                'colaboracion.book',
                [
                    'roles' => config(self::ROLES_ROL_TUTOR),
                    'class' => 'btn-primary informe book',
                    'text' => '',
                    'title' => 'Contacte',
                    'icon' => 'fa-comments'
                ]
            )
        );
        $this->panel->setBoton(
            'pendiente',
            new BotonBasico(
                "colaboracion.contacto",
                [
                    'class' => 'btn-default selecciona',
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
                    'class' => 'btn-default selecciona',
                    'icon' => 'fa fa-check',
                    'data-url' => '/api/documentacionFCT/revision'
                ]
            )
        );
    }

    /**
     * @return mixed
     */
    /*
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
        return $colaboracions->sortBy('empresa');
    }*/


    /**
     * Carrega les col·laboracions del tutor i les relacionades per centre/departament.
     *
     * A cada col·laboració "meua" li adjunta:
     * - `relacionadas`: col·laboracions del mateix centre i departament,
     *   però d'un altre cicle.
     * - `contactos`: activitats de seguiment associades a cada relacionada.
     *
     * @return \Illuminate\Support\Collection<int, \Intranet\Entities\Colaboracion>
     */
    public function search()
    {
        $colaboraciones = $this->colaboraciones()->panelListingByTutor((string) AuthUser()->dni);
        $title = $this->colaboraciones()->resolvePanelTitle($colaboraciones);
        if ($title !== null) {
            $this->titulo = ['quien' => $title];
        }

        return $colaboraciones;
    }




    /**
     * @param  Request  $request
     * @param $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request, $id)
    {
        $colaboracion = $this->findModelOrFail(
            Colaboracion::class,
            $id,
            'Col·laboració no trobada',
            ['colaboracion_id' => $id]
        );
        $this->authorize('update', $colaboracion);
        $this->validate($request, (new ColaboracionRequest())->rules(), (new ColaboracionRequest())->messages());
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
        $this->authorize('create', Colaboracion::class);
        $this->validate($request, (new ColaboracionRequest())->rules(), (new ColaboracionRequest())->messages());
        parent::store($request);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana', 1);
        return $this->showEmpresa($empresa);
    }

    private function showEmpresa($id)
    {
        return redirect()->route('empresa.detalle', ['empresa' => $id]);
    }

    /**
     * Guarda una preassignació provisional d'alumnat per a una col·laboració.
     *
     * @param Request $request
     * @param int $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePreasignacion(Request $request, int $id)
    {
        $colaboracion = $this->findModelOrFail(
            Colaboracion::class,
            $id,
            'Col·laboració no trobada',
            ['colaboracion_id' => $id]
        );
        $this->authorize('update', $colaboracion);

        $this->validate($request, [
            'idAlumno' => 'required|string',
            'estado' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $this->preasignaciones()->create(
                $colaboracion->id,
                (string) $request->idAlumno,
                (string) AuthUser()->dni,
                (string) ($request->estado ?: 'proposta'),
                $request->observaciones
            );
            Alert::success('Preassignació guardada correctament.');
        } catch (RuntimeException $e) {
            Alert::warning($e->getMessage());
        }

        Session::put('pestana', 3);
        return back();
    }

    /**
     * Descarta una preassignació existent des del panell del tutor.
     *
     * @param int $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function descartarPreasignacion(int $id)
    {
        $preasignacion = $this->wrapNotFound(
            fn () => \Intranet\Entities\ColaboracionPreasignacion::query()
                ->with('Colaboracion')
                ->findOrFail($id),
            'Preassignació no trobada',
            ['preasignacion_id' => $id]
        );
        $this->authorize('update', $preasignacion->Colaboracion);

        try {
            $this->preasignaciones()->descartar($preasignacion->id);
            Alert::success('Preassignació eliminada correctament.');
        } catch (RuntimeException $e) {
            Alert::warning($e->getMessage());
        }

        Session::put('pestana', 3);
        return back();
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
        $grupoTutor = $this->grupos()->firstByTutor($profesor);
        if (!$grupoTutor) {
            return back()->withErrors('No s\'ha trobat grup de tutoria');
        }
        $copia->idCiclo = $grupoTutor->idCiclo;
        $copia->tutor = AuthUser()->FullName;

        // para no generar más de uno por ciclo
        $validator = Validator::make($copia->toArray(), ColaboracionCrudSchema::RULES);
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
            report($exception);
            Log::warning('Error en esborrar col·laboració.', [
                'colaboracion_id' => $id,
                'error' => $exception->getMessage(),
            ]);
            Alert::danger("No es pot esborrar perquè hi ha valoracions
             fetes per a eixa col·laboració d'anys anteriors.");
        }

        Session::put('pestana', 1);
        return $this->showEmpresa($empresa);
    }

    public function live()
    {
        return view('colaboraciones.panel');
    }


}
