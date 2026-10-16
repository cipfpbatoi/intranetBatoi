<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Crud;

use Illuminate\Contracts\Validation\Validator;
use Intranet\Presentation\Crud\EmpresaCrudSchema;
use Tests\TestCase;

/** Comprova les regles del formulari d'empresa. */
class EmpresaCrudSchemaTest extends TestCase
{
    public function test_admet_identificadors_del_gerent_compatibles_amb_dni_nie_nif_i_estrangers(): void
    {
        foreach (['12345678Z', 'X1234567L', 'B12345678', 'AB-12345678'] as $identificador) {
            $this->assertTrue($this->validatorFor($identificador)->passes(), $identificador);
        }
    }

    public function test_rebutja_identificadors_del_gerent_amb_format_no_admes(): void
    {
        foreach (['ABC', '123 456 78Z', 'NIF:12345678Z'] as $identificador) {
            $this->assertTrue($this->validatorFor($identificador)->fails(), $identificador);
        }
    }

    public function test_accepta_el_camp_dependent_gva_com_a_boolea(): void
    {
        $rules = EmpresaCrudSchema::requestRules(null);

        $this->assertTrue(validator(['dependent_gva' => true], ['dependent_gva' => $rules['dependent_gva']])->passes());
        $this->assertTrue(validator(['dependent_gva' => 'no'], ['dependent_gva' => $rules['dependent_gva']])->fails());
    }

    /**
     * Crea el validador amb les dades mínimes obligatòries d'una empresa.
     */
    private function validatorFor(string $identificador): Validator
    {
        $rules = EmpresaCrudSchema::requestRules(null);

        return validator(
            ['nif_gerente' => $identificador],
            ['nif_gerente' => $rules['nif_gerente']]
        );
    }
}
