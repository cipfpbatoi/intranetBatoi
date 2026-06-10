<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Intranet\Http\Requests\TaskRequest;
use Tests\TestCase;

/**
 * Proves de validació del formulari de tasques.
 */
class TaskRequestTest extends TestCase
{
    /**
     * Verifica que el format de data del datepicker legacy valida correctament.
     */
    public function test_normalitza_vencimiento_legacy_abans_de_validar(): void
    {
        $request = TaskRequest::create('/task/create', 'POST', [
            'descripcion' => 'Avis de prova',
            'vencimiento' => '10/06/2026',
            'enlace' => str_repeat('https://example.test/document?', 20),
            'destinatario' => '1',
        ]);

        $this->prepareForValidation($request);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertSame('2026-06-10', $request->input('vencimiento'));
        $this->assertTrue($validator->passes(), (string) $validator->errors());
    }

    /**
     * Executa el hook protegit de FormRequest per provar la normalització.
     */
    private function prepareForValidation(TaskRequest $request): void
    {
        $method = new \ReflectionMethod($request, 'prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);
    }
}
