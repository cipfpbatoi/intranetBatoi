<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Empresa\EmpresaService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\IntranetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Empresa;
use Intranet\Http\PrintResources\A1Resource;
use Intranet\Presentation\Crud\EmpresaCrudSchema;
use Intranet\Services\Document\FDFPrepareService;
use Intranet\UI\Botones\BotonBasico;
use Styde\Html\Facades\Alert;

class EmpresaController extends IntranetController
{
    private const ROLES_ROL_TUTOR = 'roles.rol.tutor';

    private ?GrupoService $grupoService = null;
    private ?EmpresaService $empresaService = null;

    protected $perfil = 'profesor';
    protected $model = 'Empresa';
    protected $gridFields = EmpresaCrudSchema::GRID_FIELDS;
    protected $vista = ['show' => 'empresa','index'=>'vacia'];

    public function __construct(?GrupoService $grupoService = null, ?EmpresaService $empresaService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->empresaService = $empresaService;
        $this->formFields = EmpresaCrudSchema::FORM_FIELDS;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function empreses(): EmpresaService
    {
        if ($this->empresaService === null) {
            $this->empresaService = app(EmpresaService::class);
        }

        return $this->empresaService;
    }

    protected function search()
    {
        return $this->empreses()->listForGrid();
    }

    
    public function create($default=[])
    {
        return parent::create(['creador'=> AuthUser()->dni]);
    }
    
    public function show($id)
    {
        $activa = Session::get('pestana') ? Session::get('pestana') : 2;
        $elemento = $this->empreses()->findForShow((int) $id);
        $modelo = 'Empresa';

        $cicloTutoria = $this->grupos()->find((string) AuthUser()->GrupoTutoria)?->idCiclo;
        $misColaboracionesIds = $this->empreses()->colaboracionIdsForTutorCycle($cicloTutoria, $elemento);
        $ciclosDepartamento = $this->empreses()->departmentCycles((string) authUser()->departamento);
        $ciclosDepartamentoIds = $ciclosDepartamento->pluck('id')->all();

        return view('empresa.show', compact(
            'elemento',
            'modelo',
            'activa',
            'misColaboracionesIds',
            'cicloTutoria',
            'ciclosDepartamento',
            'ciclosDepartamentoIds'
        ));
    }
    
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("empresa.create", ['roles' => config(self::ROLES_ROL_TUTOR)]));
     }

    public function store(Request $request)
    {
        $this->authorize('create', Empresa::class);
        $id = $this->empreses()->saveFromRequest($request);

        $idCentro = $this->empreses()->createCenter($id, $request);
        $idCicloTutoria = $this->grupos()->firstByTutor(AuthUser()->dni)?->idCiclo;
        if ($idCicloTutoria) {
            $this->empreses()->createColaboration($idCentro, $request, $idCicloTutoria, AuthUser()->FullName);
        }

        return redirect()->action('EmpresaController@show', ['empresa' => $id]);
    }
    
    public function update(Request $request, $id)
    {
        $this->authorize('update', Empresa::findOrFail((int) $id));
        $elemento = Empresa::findOrFail($this->empreses()->saveFromRequest($request, $id));
        $this->empreses()->fillMissingCenterData($elemento);
        return redirect()->action('EmpresaController@show', ['empresa' => $elemento->id]);
    }

    /*
     * document ($id)
     * torna el fitxer de un model
     */

    public function document($id)
    {
        $elemento = Empresa::findOrFail($id);
        if ($elemento->fichero) {
            return response()->file(storage_path('app/' . $elemento->fichero));
        }
        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }

    public function A1($id)
    {
        return response()->file(FDFPrepareService::exec(new A1Resource(Empresa::find($id))));
    }

}
