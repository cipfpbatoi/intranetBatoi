<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Ciclo;
use Intranet\Http\Requests\CicloDualRequest;
use Intranet\Services\UI\FormBuilder;
use Styde\Html\Facades\Alert;

/**
 * Class CicloController
 * @package Intranet\Http\Controllers
 */
class CicloDualController extends Controller
{
    private ?GrupoService $grupoService = null;

    protected $model = 'Ciclo';

    public function __construct(?GrupoService $grupoService = null)
    {
        $this->grupoService = $grupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    public function edit(){
        $user = AuthUser()->dni;
        $ciclo = $this->grupos()->firstByTutorDual(AuthUser()->dni)?->ciclo;
        if ($ciclo) {
            $formulario = new FormBuilder($ciclo, [
                'id' => ['type'=>'hidden'],
                'acronim' => ['type' => 'text'],
                'llocTreball' => ['type' => 'text'],
                'dataSignaturaDual' => ['type' => 'date']
            ]);
            return view('ciclo.edit', compact('formulario'));
        }
        else {
            Alert::info('No eres tutor de Dual');
            return back();
        }
    }

    public function update(CicloDualRequest $request)
    {
        $ciclo = Ciclo::findOrFail($request->id);
        $ciclo->acronim = $request->acronim;
        $ciclo->llocTreball = $request->llocTreball;
        $ciclo->dataSignaturaDual = $request->dataSignaturaDual;
        $ciclo->save();
        return redirect()->route('dual.index');
    }

}
