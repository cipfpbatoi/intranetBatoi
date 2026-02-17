<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Activity;
use Intranet\Http\Traits\Core\Panel;
use Styde\Html\Facades\Alert;

/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class PanelColaboracionController extends IntranetController
{
    use Panel;

    private ?GrupoService $grupoService = null;

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

    public function __construct(?GrupoService $grupoService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
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
                    'title' => 'Contacte previ',
                    'icon' => 'fa-book'
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


public function search()
{
    // 1) Les teues col·laboracions
    $meves = Colaboracion::query()
        ->MiColaboracion()
        ->with(['Propietario', 'Centro', 'Centro.Empresa', 'Ciclo'])
        ->get();

    if ($meves->isEmpty()) {
        return $meves;
    }
    $this->titulo = ['quien' => optional($meves->first()->Ciclo)->literal];

    // clau parella: centre|departament-del-cicle
    $pairKey = fn($c) => $c->idCentro.'|'.optional($c->Ciclo)->departamento;

    // parelles úniques (centre, departament del cicle)
    $parelles = $meves->filter(fn($c) => optional($c->Ciclo)->departamento)
        ->map(fn($c) => ['idCentro' => $c->idCentro, 'departamento' => $c->Ciclo->departamento])
        ->unique(fn($p) => $p['idCentro'].'|'.$p['departamento'])
        ->values();

    // 2) Col·laboracions relacionades: mateix centre+dept (via Ciclo), però d’un altre cicle
    $relacionades = Colaboracion::query()
        ->with(['Ciclo', 'Propietario'])
        ->whereNotIn('id', $meves->pluck('id'))
        ->where(function ($q) use ($parelles) {
            foreach ($parelles as $p) {
                $q->orWhere(function ($qq) use ($p) {
                    $qq->where('idCentro', $p['idCentro'])
                       ->whereHas('Ciclo', function ($qh) use ($p) {
                           $qh->where('departamento', $p['departamento']);
                       });
                });
            }
        })
        ->get();

    // filtre: d’un altre cicle
    $relacionades = $relacionades->filter(function ($r) use ($meves) {
        // si tens 'ciclo_id' en lloc de 'idCiclo', canvia-ho
        $rIdCiclo = $r->idCiclo ?? $r->ciclo_id;
        // hi ha almenys una "teva" en el mateix parell amb cicle diferent?
        return $meves->contains(function ($c) use ($r, $rIdCiclo) {
            $cIdCiclo = $c->idCiclo ?? $c->ciclo_id;
            return $c->idCentro == $r->idCentro
                && optional($c->Ciclo)->departamento === optional($r->Ciclo)->departamento
                && $cIdCiclo !== $rIdCiclo;
        });
    })->values();

    // 3) Tots els Activity d'eixes relacionades, d’una tacada, i agrupats
    $relIds = $relacionades->pluck('id')->all();

    $activitiesByColab = Activity::query()
        ->modelo('Colaboracion')
        ->notUpdate()
        ->whereIn('model_id', $relIds)     // <-- CANVIA 'target_id' pel camp que realment usa el teu scope id($id)
        ->orderBy('created_at')
        ->get()
        ->groupBy('model_id');             // <-- CANVIA igualment si cal

    // Agrupem relacionades per parella centre|dept
    $relacionadesPerParella = $relacionades->groupBy($pairKey);

    // Enganxem a cada "teva" les relacionades + els seus contactes
    $meves->each(function ($c) use ($pairKey, $relacionadesPerParella, $activitiesByColab) {
        $llista = $relacionadesPerParella->get($pairKey($c), collect());

        // assignem els contactes (activities) a cada relacionada
        $llista->each(function ($rel) use ($activitiesByColab) {
            $rel->contactos = $activitiesByColab->get($rel->id, collect());
        });

        $c->relacionadas = $llista->values();
    });

    // ordenació opcional
    return $meves->sortBy(function ($c) {
        return $c->empresa
            ?? optional(optional($c->Centro)->Empresa)->nombre
            ?? optional($c->Centro)->nombre
            ?? '';
    })->values();
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
        $grupoTutor = $this->grupos()->firstByTutor($profesor);
        if (!$grupoTutor) {
            return back()->withErrors('No s\'ha trobat grup de tutoria');
        }
        $copia->idCiclo = $grupoTutor->idCiclo;
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

    public function live()
    {
        return view('colaboraciones.panel');
    }


}
