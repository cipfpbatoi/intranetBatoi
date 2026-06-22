<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Intranet\Presentation\Crud\ComisionCrudSchema;
use Tests\TestCase;

/**
 * Tests de regressió per a l'esquema de validació de comissions.
 */
class ComisionCrudSchemaTest extends TestCase
{
    /**
     * Verifica que una comissió feta hui no quede bloquejada per la regla de data futura.
     */
    public function test_professor_pot_crear_comissio_del_dia_actual_amb_hora_valida(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 22, 16, 0, 0));

        try {
            $validator = Validator::make([
                'servicio' => 'Assistència a reunió',
                'kilometraje' => 0,
                'desde' => '2026-06-22 08:00:00',
                'hasta' => '2026-06-22 14:00:00',
                'alojamiento' => 0,
                'comida' => 0,
                'gastos' => 0,
                'medio' => 2,
                'marca' => '',
                'matricula' => '',
            ], ComisionCrudSchema::requestRules(false));

            $this->assertFalse($validator->fails(), (string) $validator->errors());
        } finally {
            Carbon::setTestNow();
        }
    }
}
