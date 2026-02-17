<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Empresa\EmpresaService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Empresa;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Http\PrintResources\A1Resource;
use Intranet\Presentation\Crud\EmpresaCrudSchema;
use Intranet\Services\Document\FDFPrepareService;
use Intranet\UI\Botones\BotonBasico;
use Styde\Html\Facades\Alert;

class EmpresaController extends IntranetController
{
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
        $this->panel->setBoton('index', new BotonBasico("empresa.create", ['roles' => config('roles.rol.tutor')]));
     }

    public function store(Request $request)
    {
        $id = $this->realStore($request);

        $idCentro = $this->createCenter($id, $request);
        $idCicloTutoria = $this->grupos()->firstByTutor(AuthUser()->dni)?->idCiclo;
        if ($idCicloTutoria) {
            $this->createColaboration($idCentro, $request, $idCicloTutoria);
        }

        return redirect()->action('EmpresaController@show', ['empresa' => $id]);
    }



    private function createCenter($id, Request $request)
    {
        $centro = new Centro();
        $centro->idEmpresa = $id;
        $centro->direccion = $request->direccion;
        $centro->nombre = $request->nombre;
        $centro->localidad = $request->localidad;
        $centro->save();
        return $centro->id;
    }
    private function createColaboration($id, Request $request, $idCicloTutoria)
    {
        $colaboracion = new Colaboracion();
        $colaboracion->idCentro = $id;
        $colaboracion->telefono = $request->telefono;
        $colaboracion->email = $request->email;
        $colaboracion->puestos = 1;
        $colaboracion->tutor = AuthUser()->FullName;
        $colaboracion->idCiclo = $idCicloTutoria;
        $colaboracion->save();
        return $colaboracion->id;
    }
    
    protected function realStore(Request $request, $id = null)
    {
        $request = $this->normalitzaEmpresaRequest($request);
        $this->validate($request, EmpresaCrudSchema::requestRules($id));

        $elemento = $id ? Empresa::findOrFail($id) : new Empresa(); //busca si hi ha
        return $elemento->fillAll($request);        // ompli i guarda
    }
    
    public function update(Request $request, $id)
    {
        $elemento = Empresa::find($this->realStore($request, $id));

        foreach ($elemento->centros as $centro) {
            $touched = false;
            if ($centro->direccion == '') {
                $centro->direccion = $elemento->direccion;
                $touched = true;
            }
            if ($centro->localidad == '') {
                $centro->localidad = $elemento->localidad;
                $touched = true;
            }
            if ($centro->nombre == '') {
                $centro->nombre = $elemento->nombre;
                $touched = true;
            }
            if ($touched) {
                $centro->save();
            }
        }
        return redirect()->action('EmpresaController@show', ['empresa' => $elemento->id]);
    }

    private function normalitzaEmpresaRequest(Request $request): Request
    {
        $checkboxes = ['europa', 'sao', 'dual', 'delitos', 'menores'];
        $normalized = [];

        foreach ($checkboxes as $field) {
            $normalized[$field] = $request->boolean($field);
        }

        if ($request->filled('cif')) {
            $normalized['cif'] = strtoupper((string) $request->input('cif'));
        }

        $request->merge($normalized);

        return $request;
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
