<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Intranet\Http\Requests\LoteRequest;
use Tests\TestCase;

class LoteRequestFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('lote_request_feature_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('lotes');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_store_falla_si_registre_ja_existeix(): void
    {
        DB::table('lotes')->insert([
            'registre' => 'LOT-001',
        ]);

        $request = LoteRequest::create('/lote/create', 'POST', [
            'registre' => 'LOT-001',
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('registre', $validator->errors()->toArray());
    }

    public function test_update_permet_mantindre_el_mateix_registre(): void
    {
        DB::table('lotes')->insert([
            'registre' => 'LOT-001',
        ]);

        $route = new class () {
            public function parameter(string $key, $default = null)
            {
                return $key === 'id' ? 'LOT-001' : $default;
            }
        };

        $request = LoteRequest::create('/lote/LOT-001/edit', 'PUT', [
            'registre' => 'LOT-001',
        ]);
        $request->setRouteResolver(static fn () => $route);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_update_falla_si_canvia_a_registre_ja_ocupat(): void
    {
        DB::table('lotes')->insert([
            ['registre' => 'LOT-001'],
            ['registre' => 'LOT-002'],
        ]);

        $route = new class () {
            public function parameter(string $key, $default = null)
            {
                return $key === 'id' ? 'LOT-001' : $default;
            }
        };

        $request = LoteRequest::create('/lote/LOT-001/edit', 'PUT', [
            'registre' => 'LOT-002',
        ]);
        $request->setRouteResolver(static fn () => $route);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('registre', $validator->errors()->toArray());
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('lotes')) {
            Schema::connection('sqlite')->create('lotes', function (Blueprint $table): void {
                $table->string('registre', 12)->primary();
            });
        }
    }
}
