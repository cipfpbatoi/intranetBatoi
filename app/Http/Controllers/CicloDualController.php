<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Ciclo;
use Intranet\Entities\Grupo;
use Intranet\Http\Requests\CicloDualRequest;
use Intranet\Services\FormBuilder;
use Styde\Html\Facades\Alert;

/**
 * Class CicloController
 * @package Intranet\Http\Controllers
 */
class CicloDualController extends Controller
{
    protected $model = 'Ciclo';

    public function edit(){
        $user = AuthUser()->dni;
        $ciclo = Grupo::where('tutorDual',AuthUser()->dni)->first()->ciclo;
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
