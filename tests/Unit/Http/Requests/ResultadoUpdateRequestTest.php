<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Intranet\Http\Requests\ResultadoUpdateRequest;
use Tests\TestCase;

/**
 * Regressió de la validació d'edició de resultats.
 */
class ResultadoUpdateRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('resultados');

        $schema->create('resultados', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloGrupo')->nullable();
            $table->unsignedInteger('evaluacion')->nullable();
        });
    }

    public function test_update_rules_reject_duplicate_modulo_grupo_and_evaluacion(): void
    {
        DB::table('resultados')->insert([
            [
                'id' => 6480,
                'idModuloGrupo' => 123,
                'evaluacion' => 2,
            ],
            [
                'id' => 7001,
                'idModuloGrupo' => 718,
                'evaluacion' => 2,
            ],
        ]);

        $request = $this->makeRequest([
            'idModuloGrupo' => 718,
            'evaluacion' => 2,
            'matriculados' => 20,
            'evaluados' => 18,
            'aprobados' => 15,
            'udProg' => 12,
            'udImp' => 10,
        ], 6480);

        $validator = Validator::make(
            $request->all(),
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('evaluacion', $validator->errors()->messages());
    }

    public function test_update_rules_allow_the_same_record_values(): void
    {
        DB::table('resultados')->insert([
            'id' => 6480,
            'idModuloGrupo' => 718,
            'evaluacion' => 2,
        ]);

        $request = $this->makeRequest([
            'idModuloGrupo' => 718,
            'evaluacion' => 2,
            'matriculados' => 20,
            'evaluados' => 18,
            'aprobados' => 15,
            'udProg' => 12,
            'udImp' => 10,
        ], 6480);

        $validator = Validator::make(
            $request->all(),
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * Construïx una petició d'update amb el paràmetre de ruta resolt.
     *
     * @param array<string, int> $payload
     * @param int $resultadoId
     * @return ResultadoUpdateRequest
     */
    private function makeRequest(array $payload, int $resultadoId): ResultadoUpdateRequest
    {
        $request = ResultadoUpdateRequest::create("/resultado/{$resultadoId}/edit", 'PUT', $payload);
        $route = new Route('PUT', '/resultado/{resultado}/edit', []);
        $route->setParameter('resultado', $resultadoId);
        $request->setRouteResolver(static fn () => $route);

        return $request;
    }
}
