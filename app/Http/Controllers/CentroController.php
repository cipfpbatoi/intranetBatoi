<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;
use Intranet\Http\Requests\EmpresaCentroRequest;
use Response;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;

/**
 * Class CentroController
 * @package Intranet\Http\Controllers
 */
class CentroController extends IntranetController
{


    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Centro';


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        Session::put('pestana', 2);
        return $this->showEmpresa($request->idEmpresa);
    }

    private function showEmpresa($id)
    {
        return redirect()->action('EmpresaController@show', ['empresa' => $id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        parent::store($request);
        Session::put('pestana',2);
        return $this->showEmpresa($request->idEmpresa);
    }



    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $centro = Centro::find($id);
        $empresa = Centro::find($id)->idEmpresa;
        $misColaboraciones =Colaboracion::Micolaboracion($empresa)->count();
        if (rolesUser(config('roles.rol.administrador'))) {
            parent::destroy($id);
        } else {
            if ($centro->Colaboraciones->count() == $misColaboraciones){
                try {
                    parent::destroy($id);
                } catch (QueryException $exception){
                    Alert::danger("No es pot esborrar perquè hi ha valoracions fetes per a eixe centre d'anys anteriors.");
                }
            } else {
                Alert::danger("Eixe centre te col·laboracions d'altres cicles. Esborra la col·laboració del teu cicle");
            }
        }
        Session::put('pestana',2);
        return $this->showEmpresa($empresa);
    }

    public function empresaCreateCentro(EmpresaCentroRequest $request, $id)
    {
        $centro = Centro::findOrFail($id);
        $empresaAnt = $centro->Empresa;
        if ($empresaAnt->concierto == $request->concierto) {
            $empresaAnt->concierto = null;
            $empresaAnt->save();
        }

        $empresa = new Empresa([
            'cif' => $request->cif,
            'concierto' => $request->concierto,
            'nombre' => $centro->nombre,
            'email' => $request->email,
            'direccion' => $centro->direccion,
            'localidad' => $centro->localidad,
            'telefono' => $request->telefono,
        ]);
        $empresa->save();
        $centro->idEmpresa = $empresa->id;
        $centro->idSao = null;
        $centro->save();
        return $this->showEmpresa($empresa->id);
    }
    

}
