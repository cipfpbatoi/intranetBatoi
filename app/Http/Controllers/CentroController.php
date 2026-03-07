<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\QueryException;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\CentroRequest;
use Intranet\Http\Requests\EmpresaCentroRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Class CentroController
 * @package Intranet\Http\Controllers
 */
class CentroController extends ModalController
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
    public function update(CentroRequest $request, $id)
    {
        $this->persist($request, $id);
        Session::put('pestana', 2);
        return $this->showEmpresa($request->idEmpresa);
    }

    private function showEmpresa($id)
    {
        return redirect()->route('empresa.detalle', ['empresa' => $id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CentroRequest $request)
    {
        $this->persist($request);
        Session::put('pestana',2);
        return $this->showEmpresa($request->idEmpresa);
    }



    /**
     * @param $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $centro = $this->findModelOrFail(Centro::class, $id, 'Centre no trobat', ['centro_id' => $id]);
        $empresa = $centro->idEmpresa;

        if (isAdmin()) {
            parent::destroy($id);
        } else {
            $misColaboraciones = Colaboracion::MiColaboracion($empresa)->count();

            if ($centro->colaboraciones()->count() == $misColaboraciones){
                try {
                    parent::destroy($id);
                } catch (QueryException $exception){
                    report($exception);
                    Log::warning('No es pot esborrar el centre per restriccions de claus.', [
                        'centro_id' => $id,
                        'empresa_id' => $empresa,
                        'error' => $exception->getMessage(),
                    ]);
                    Alert::danger("No es pot esborrar perquè hi ha valoracions fetes per a eixe centre d'anys anteriors.");
                }
            } else {
                Alert::danger("Eixe centre te col·laboracions d'altres cicles. Esborra la col·laboració del teu cicle");
            }
        }
        Session::put('pestana',2);
        return $this->showEmpresa($empresa);
    }

    /**
     * @param EmpresaCentroRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function empresaCreateCentro(EmpresaCentroRequest $request, $id)
    {
        $centro = $this->findModelOrFail(Centro::class, $id, 'Centre no trobat', ['centro_id' => $id]);
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
