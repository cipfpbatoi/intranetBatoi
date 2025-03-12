<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Empresa;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Intranet\Http\PrintResources\A1Resource;
use Intranet\Services\FDFPrepareService;
use Response;
use Intranet\Botones\BotonBasico;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Input;

class EmpresaController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Empresa';
    protected $gridFields = ['concierto', 'nombre', 'direccion', 'localidad', 'telefono', 'email', 'cif', 'actividad'];
    protected $vista = ['show' => 'empresa','grid'=>'vacia'];

    
    public function create($default=[])
    {
        return parent::create(['creador'=> AuthUser()->dni]);
    }
    
    public function show($id)
    {
        $activa = Session::get('pestana') ? Session::get('pestana') : 2;
        $elemento = Empresa::findOrFail($id);
        $modelo = 'Empresa';
        $misColaboraciones = Grupo::find(AuthUser()->GrupoTutoria)->Ciclo->Colaboraciones??collect();
        return view('empresa.show' , compact('elemento', 'modelo', 'activa', 'misColaboraciones'));
    }
    
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("empresa.create", ['roles' => config('roles.rol.tutor')]));
     }

    public function store(Request $request)
    {
        $dades = $request->except('cif');
        $dades['cif'] = strtoupper($request->cif);
        if (Empresa::where('cif', strtoupper($request->cif))->count()) {
            return back()->withInput($request->all())->withErrors('CIF duplicado');
        }

        $id = $this->realStore(subsRequest($request, ['cif'=>strtoupper($request->cif)]));

        $idCentro = $this->createCenter($id, $request);
        if (isset(Grupo::select('idCiclo')->QTutor(AuthUser()->dni)->first()->idCiclo)) {
            $this->createColaboration($idCentro, $request);
        }

        return redirect()->action('EmpresaController@show', ['empresa' => $id]);
    }



    private function createCenter($id, $request)
    {
        $centro = new Centro();
        $centro->idEmpresa = $id;
        $centro->direccion = $request->direccion;
        $centro->nombre = $request->nombre;
        $centro->localidad = $request->localidad;
        $centro->save();
        return $centro->id;
    }
    private function createColaboration($id, $request)
    {
        $colaboracion = new Colaboracion();
        $colaboracion->idCentro = $id;
        $colaboracion->telefono = $request->telefono;
        $colaboracion->email = $request->email;
        $colaboracion->puestos = 1;
        $colaboracion->tutor = AuthUser()->FullName;
        $colaboracion->idCiclo = Grupo::select('idCiclo')->QTutor(AuthUser()->dni)->first()->idCiclo;
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

        $touched = false;
        foreach ($elemento->centros as $centro) {
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
        }
        if ($touched) {
            $centro->save();
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
