<?php

/**
 * @deprecated Codi legacy de DUAL/FCTDUAL.
 * Mantingut temporalment per compatibilitat.
 */

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Ciclo;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\CicloDualRequest;
use Intranet\Services\UI\FormBuilder;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Controlador per a la gestió de dades duals del cicle.
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

    /**
     * @param CicloDualRequest $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CicloDualRequest $request)
    {
        $ciclo = $this->findModelOrFail(Ciclo::class, $request->id, 'Cicle no trobat', ['ciclo_id' => $request->id]);
        $ciclo->acronim = $request->acronim;
        $ciclo->llocTreball = $request->llocTreball;
        $ciclo->dataSignaturaDual = $request->dataSignaturaDual;
        $ciclo->save();
        return redirect()->route('controlDual.index');
    }

}
