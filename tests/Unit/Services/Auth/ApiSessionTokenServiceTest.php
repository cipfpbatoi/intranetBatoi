<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Services\Auth\ApiSessionTokenService;
use Tests\TestCase;

class ApiSessionTokenServiceTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_session_token_service_test.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('personal_access_tokens');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_issue_and_revoke_token_in_session(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'PSTS01',
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'api_token' => 'legacy-sts-01',
            'rol' => config('roles.rol.profesor'),
            'departamento' => 1,
            'activo' => 1,
        ]);

        $profesor = Profesor::on('sqlite')->findOrFail('PSTS01');
        $service = app(ApiSessionTokenService::class);

        $plainToken = $service->issueForProfesor($profesor, 'unit-test');

        $this->assertIsString($plainToken);
        $this->assertStringContainsString('|', $plainToken);
        $this->assertNotNull(session('api_access_token'));
        $this->assertNotNull(session('api_access_token_id'));
        $this->assertSame(1, DB::table('personal_access_tokens')->count());

        $service->revokeCurrentFromSession();

        $this->assertNull(session('api_access_token'));
        $this->assertNull(session('api_access_token_id'));
        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('api_token', 80)->nullable();
                $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
                $table->unsignedInteger('departamento')->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('personal_access_tokens')) {
            Schema::connection('sqlite')->create('personal_access_tokens', function (Blueprint $table): void {
                $table->id();
                $table->string('tokenable_type');
                $table->string('tokenable_id');
                $table->text('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }
}
