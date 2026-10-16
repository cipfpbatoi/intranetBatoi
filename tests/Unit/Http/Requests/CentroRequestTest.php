<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use Intranet\Http\Requests\CentroRequest;
use Tests\TestCase;

/**
 * Proves de regressió de les regles compartides dels centres.
 */
class CentroRequestTest extends TestCase
{
    /**
     * El formulari web ha de continuar exigint l'empresa del centre.
     */
    public function test_regles_del_formulari_web_exigeixen_empresa(): void
    {
        $request = new CentroRequest();

        $this->assertSame([
            'nombre' => 'required',
            'direccion' => 'required',
            'localidad' => 'required',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'idEmpresa' => 'required',
        ], $request->rules());
    }

    /**
     * L'actualització API ha de reutilitzar les regles sense exigir l'empresa.
     */
    public function test_regles_compartides_per_a_api_no_exigeixen_empresa(): void
    {
        $this->assertSame([
            'nombre' => 'required',
            'direccion' => 'required',
            'localidad' => 'required',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ], CentroRequest::sharedRules(false));
    }
}
