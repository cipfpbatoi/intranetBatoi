<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Empresa;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Grupo;
use Intranet\Http\PrintResources\A1Resource;
use Intranet\Services\Document\FDFPrepareService;
use Intranet\UI\Botones\BotonBasico;
use Styde\Html\Facades\Alert;

class EmpresaController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Empresa';
    protected $gridFields = ['concierto', 'nombre', 'direccion', 'localidad', 'telefono', 'email', 'cif', 'actividad'];
    protected $vista = ['show' => 'empresa','index'=>'vacia'];

    protected function search()
    {
        return Empresa::query()
            ->select(['id', 'concierto', 'nombre', 'direccion', 'localidad', 'telefono', 'email', 'cif', 'actividad'])
            ->orderBy('nombre')
            ->get();
    }

    
    public function create($default=[])
    {
        return parent::create(['creador'=> AuthUser()->dni]);
    }
    
    public function show($id)
    {
        $activa = Session::get('pestana') ? Session::get('pestana') : 2;
        $elemento = Empresa::with([
            'centros.colaboraciones.ciclo',
            'centros.instructores',
        ])->findOrFail($id);
        $modelo = 'Empresa';

        $cicloTutoria = optional(Grupo::find(AuthUser()->GrupoTutoria))->idCiclo;
        $misColaboracionesIds = collect();
        if ($cicloTutoria) {
            $misColaboracionesIds = Colaboracion::query()
                ->where('idCiclo', $cicloTutoria)
                ->whereIn('idCentro', $elemento->centros->pluck('id'))
                ->pluck('id');
        }

        $ciclosDepartamento = Ciclo::query()
            ->where('departamento', authUser()->departamento)
            ->get();
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
        $cif = strtoupper((string) $request->cif);
        if (Empresa::where('cif', $cif)->exists()) {
            return back()->withInput($request->all())->withErrors('CIF duplicado');
        }

        $id = $this->realStore(subsRequest($request, ['cif' => $cif]));

        $idCentro = $this->createCenter($id, $request);
        $idCicloTutoria = Grupo::query()->QTutor(AuthUser()->dni)->value('idCiclo');
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
        $elemento = $id ? Empresa::findOrFail($id) : new Empresa(); //busca si hi ha
        if ($id) {
            $elemento->setRule('concierto', $elemento->getRule('concierto').','.$id);
        }
        $this->validateAll($request, $elemento);    // valida les dades

        return $elemento->fillAll($request);        // ompli i guarda
    }
    
    public function update(Request $request, $id)
    {
        $elemento = Empresa::find($this->realStore(subsRequest($request, ['cif'=>strtoupper($request->cif)]), $id));

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
