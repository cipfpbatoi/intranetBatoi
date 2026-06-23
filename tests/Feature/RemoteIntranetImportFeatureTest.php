<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

/**
 * Proves d'importació autenticada des d'una intranet remota.
 */
class RemoteIntranetImportFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('remote_intranet_import_feature_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        config([
            'services.remote_intranet.url' => 'https://remote-intranet.test/api',
            'services.remote_intranet.api_token' => 'legacy-token-test',
            'services.remote_intranet.device_name' => 'phpunit',
            'services.remote_intranet.timeout' => 5,
        ]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_importacio_remota_crea_i_actualitza_alumno_fcts_amb_bearer(): void
    {
        $admin = $this->insertProfesor('ADM001', (int) config('roles.rol.administrador'));
        $this->actingAs($admin, 'profesor');

        DB::table('alumno_fcts')->insert([
            'id' => 1,
            'idFct' => 10,
            'idAlumno' => '10802710',
            'idSao' => 'SAO-10',
            'horas' => 100,
            'pg0301' => 0,
        ]);

        Http::fake([
            'https://remote-intranet.test/api/auth/exchange' => Http::response([
                'success' => true,
                'data' => ['access_token' => 'bearer-token-test'],
            ]),
            'https://remote-intranet.test/api/alumnofct' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'idFct' => 10,
                        'idAlumno' => '10802710',
                        'idSao' => 'SAO-10',
                        'horas' => 380,
                        'pg0301' => 1,
                    ],
                    [
                        'idFct' => 11,
                        'idAlumno' => '10802711',
                        'idSao' => 'SAO-11',
                        'horas' => 400,
                        'idProfesor' => 'ADM001',
                    ],
                ],
            ]),
        ]);

        $response = $this
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('import.remote.alumnofct'));

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.created', 1);
        $response->assertJsonPath('data.updated', 1);
        $response->assertJsonPath('data.skipped', 0);
        $response->assertJsonPath('data.errors', 0);

        $this->assertSame(2, DB::table('alumno_fcts')->count());
        $this->assertSame(380, (int) DB::table('alumno_fcts')->where('idSao', 'SAO-10')->value('horas'));
        $this->assertSame(1, (int) DB::table('alumno_fcts')->where('idSao', 'SAO-10')->value('pg0301'));
        $this->assertSame(400, (int) DB::table('alumno_fcts')->where('idSao', 'SAO-11')->value('horas'));

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://remote-intranet.test/api/alumnofct'
                && $request->hasHeader('Authorization', 'Bearer bearer-token-test');
        });
    }

    public function test_importacio_remota_no_modifica_dades_si_falla_autenticacio(): void
    {
        $admin = $this->insertProfesor('ADM002', (int) config('roles.rol.administrador'));
        $this->actingAs($admin, 'profesor');

        DB::table('alumno_fcts')->insert([
            'id' => 2,
            'idFct' => 20,
            'idAlumno' => '10802720',
            'idSao' => 'SAO-20',
            'horas' => 120,
        ]);

        Http::fake([
            'https://remote-intranet.test/api/auth/exchange' => Http::response([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401),
        ]);

        $response = $this
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('import.remote.alumnofct'));

        $response->assertStatus(502);
        $response->assertJsonPath('success', false);
        $this->assertSame(1, DB::table('alumno_fcts')->count());
        $this->assertSame(120, (int) DB::table('alumno_fcts')->where('idSao', 'SAO-20')->value('horas'));
    }

    public function test_importacio_remota_rebutja_usuaris_no_administradors(): void
    {
        $profesor = $this->insertProfesor('PRO001', (int) config('roles.rol.profesor'));
        $this->actingAs($profesor, 'profesor');

        $response = $this
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->from('/import')
            ->post(route('import.remote.alumnofct'));

        $response->assertStatus(403);
        Http::assertNothingSent();
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
            $table->string('api_token', 80)->nullable();
            $table->unsignedInteger('departamento')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idFct')->nullable();
            $table->string('idAlumno', 20)->nullable();
            $table->string('idProfesor', 10)->nullable();
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->unsignedTinyInteger('calProyecto')->nullable();
            $table->unsignedTinyInteger('actas')->default(0);
            $table->unsignedTinyInteger('insercion')->default(0);
            $table->unsignedInteger('horas')->default(0);
            $table->text('valoracio')->nullable();
            $table->unsignedTinyInteger('correoAlumno')->default(0);
            $table->unsignedTinyInteger('pg0301')->default(0);
            $table->decimal('beca', 8, 2)->default(0);
            $table->unsignedTinyInteger('a56')->default(0);
            $table->string('idSao')->nullable();
            $table->unsignedInteger('realizadas')->default(0);
            $table->unsignedTinyInteger('horas_diarias')->default(0);
            $table->date('actualizacion')->nullable();
            $table->unsignedTinyInteger('autorizacion')->default(0);
            $table->unsignedTinyInteger('flexible')->default(0);
        });
    }

    private function insertProfesor(string $dni, int $rol): Profesor
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => $dni . '@example.test',
            'rol' => $rol,
            'departamento' => 1,
            'activo' => 1,
        ]);

        return Profesor::on('sqlite')->findOrFail($dni);
    }
}
