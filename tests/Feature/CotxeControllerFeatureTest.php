<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Intranet\Entities\Cotxe;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\API\CotxeController as ApiCotxeController;
use Intranet\Presentation\Crud\CotxeCrudSchema;
use Intranet\Services\HR\FitxatgeService;
use Intranet\Services\School\CotxeAccessService;
use Intranet\Services\UI\FormBuilder;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * Proves de regressió del controlador de vehicles del professorat.
 */
class CotxeControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = sys_get_temp_dir() . '/cotxe_controller_feature_testing.sqlite';
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        config(['logging.channels.parking' => ['driver' => 'errorlog', 'level' => 'debug']]);

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        Schema::connection('sqlite')->dropIfExists('cotxe_acces_reserves');
        Schema::connection('sqlite')->dropIfExists('cotxe_accessos');
        Schema::connection('sqlite')->dropIfExists('cotxes');
        Schema::connection('sqlite')->dropIfExists('menus');
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_alta_i_edicio_normalitzen_espais_guions_i_minuscules(): void
    {
        $this->insertProfesor('COTXE01');
        $professor = Profesor::on('sqlite')->findOrFail('COTXE01');

        $createResponse = $this
            ->actingAs($professor, 'profesor')
            ->post(route('cotxe.store'), [
                'matricula' => ' 12 34-abc ',
                'marca' => 'Vehicle normalitzat',
            ]);

        $createResponse->assertStatus(302);
        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cotxes', [
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABC',
        ]);

        $cotxeId = (int) DB::table('cotxes')->value('id');
        $updateResponse = $this
            ->actingAs($professor, 'profesor')
            ->put(route('cotxe.update', ['id' => $cotxeId]), [
                'matricula' => ' 56-78 def ',
                'marca' => 'Vehicle editat',
            ]);

        $updateResponse->assertStatus(302);
        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cotxes', [
            'id' => $cotxeId,
            'matricula' => '5678DEF',
        ]);
    }

    public function test_alta_rebutja_matricules_equivalents_i_caracters_no_permesos(): void
    {
        $this->insertProfesor('COTXE01');
        $professor = Profesor::on('sqlite')->findOrFail('COTXE01');

        Cotxe::create([
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABC',
            'marca' => 'Vehicle existent',
        ]);

        $duplicateResponse = $this
            ->actingAs($professor, 'profesor')
            ->post(route('cotxe.store'), [
                'matricula' => '1234-abc',
                'marca' => 'Vehicle duplicat',
            ]);

        $duplicateResponse->assertSessionHasErrors('matricula');
        $this->assertSame(1, DB::table('cotxes')->count());

        $invalidResponse = $this
            ->actingAs($professor, 'profesor')
            ->post(route('cotxe.store'), [
                'matricula' => '1234/XYZ',
                'marca' => 'Vehicle invàlid',
            ]);

        $invalidResponse->assertSessionHasErrors('matricula');
        $this->assertSame(1, DB::table('cotxes')->count());
    }

    public function test_camera_reconeix_una_matricula_amb_espais_i_guions(): void
    {
        $this->insertProfesor('COTXE01');
        Cotxe::create([
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABC',
            'marca' => 'Vehicle autoritzat',
        ]);

        $access = Mockery::mock(CotxeAccessService::class);
        $access->shouldReceive('reservarAcces')->once()->with('COTXE01', 'entrada')->andReturnTrue();
        $access->shouldReceive('obrirIPortaAmbReintent')->once()->andReturn([
            'porta_oberta' => true,
            'intents' => 1,
            'error' => null,
        ]);
        $access->shouldReceive('registrarAcces')->once();
        $access->shouldReceive('finalitzarReservaAcces')->once()->with('COTXE01', 'entrada', 30);

        $fitxatge = Mockery::mock(FitxatgeService::class);
        $fitxatge->shouldReceive('fitxar')->once()->with('COTXE01')->andReturnFalse();

        $request = Request::create(
            '/api/eventPorta',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['plate' => '12 34-abc', 'device' => 'camera-1'], JSON_THROW_ON_ERROR)
        );

        $response = (new ApiCotxeController($access, $fitxatge))->eventEntrada($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Porta oberta (entrada)', $response->getData(true)['status']);
    }

    public function test_una_lectura_hamming_d_entrada_es_resol_i_es_deduplica_per_propietari(): void
    {
        $this->insertProfesor('COTXE01');
        Cotxe::create([
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABC',
            'marca' => 'Vehicle autoritzat',
        ]);

        $access = Mockery::mock(CotxeAccessService::class);
        $access->shouldReceive('reservarAcces')->once()->with('COTXE01', 'entrada')->andReturnTrue();
        $access->shouldReceive('obrirIPortaAmbReintent')->once()->andReturn([
            'porta_oberta' => true,
            'intents' => 1,
            'error' => null,
        ]);
        $access->shouldReceive('registrarAcces')->once();
        $access->shouldReceive('finalitzarReservaAcces')->once()->with('COTXE01', 'entrada', 30);

        $fitxatge = Mockery::mock(FitxatgeService::class);
        $fitxatge->shouldReceive('fitxar')->once()->with('COTXE01')->andReturnTrue();

        $request = Request::create(
            '/api/eventPorta',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['plate' => '1234ABD'], JSON_THROW_ON_ERROR)
        );

        $response = (new ApiCotxeController($access, $fitxatge))->eventEntrada($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Porta oberta (entrada)', $response->getData(true)['status']);
    }

    public function test_una_lectura_hamming_d_eixida_usa_el_mateix_flux_autoritzat(): void
    {
        $this->insertProfesor('COTXE01');
        Cotxe::create([
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABC',
            'marca' => 'Vehicle autoritzat',
        ]);

        $access = Mockery::mock(CotxeAccessService::class);
        $access->shouldReceive('reservarAcces')->once()->with('COTXE01', 'eixida')->andReturnTrue();
        $access->shouldReceive('obrirIPortaAmbReintent')->once()->andReturn([
            'porta_oberta' => true,
            'intents' => 1,
            'error' => null,
        ]);
        $access->shouldReceive('registrarAcces')->once();
        $access->shouldReceive('finalitzarReservaAcces')->once()->with('COTXE01', 'eixida', 30);

        $fitxatge = Mockery::mock(FitxatgeService::class);
        $fitxatge->shouldReceive('fitxar')->once()->with('COTXE01')->andReturnTrue();

        $request = Request::create(
            '/api/eventPortaSortida',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['license_plate' => '1234ABD'], JSON_THROW_ON_ERROR)
        );

        $response = (new ApiCotxeController($access, $fitxatge))->eventSortida($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Porta oberta (eixida)', $response->getData(true)['status']);
    }

    public function test_la_fallada_final_de_la_domotica_es_auditada_com_a_autoritzada(): void
    {
        $this->insertProfesor('COTXE01');
        Cotxe::create([
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABC',
            'marca' => 'Vehicle autoritzat',
        ]);

        $access = Mockery::mock(CotxeAccessService::class);
        $access->shouldReceive('reservarAcces')->once()->with('COTXE01', 'entrada')->andReturnTrue();
        $access->shouldReceive('obrirIPortaAmbReintent')->once()->andReturn([
            'porta_oberta' => false,
            'intents' => 2,
            'error' => 'sense_resposta_satisfactoria',
        ]);
        $access->shouldReceive('registrarAcces')->once()->withArgs(function (...$arguments): bool {
            return $arguments[1] === true
                && $arguments[2] === false
                && $arguments[9] === 2
                && $arguments[10] === 'sense_resposta_satisfactoria';
        });
        $access->shouldReceive('finalitzarReservaAcces')->once()->with('COTXE01', 'entrada', 30);

        $fitxatge = Mockery::mock(FitxatgeService::class);
        $fitxatge->shouldReceive('fitxar')->once()->with('COTXE01')->andReturnTrue();

        $request = Request::create(
            '/api/eventPorta',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['plate' => '1234ABC'], JSON_THROW_ON_ERROR)
        );

        $response = (new ApiCotxeController($access, $fitxatge))->eventEntrada($request);

        $this->assertSame('No s\'ha pogut obrir la porta', $response->getData(true)['status']);
    }

    public function test_hamming_admet_la_n_amb_longitud_unicode(): void
    {
        $this->insertProfesor('COTXE01');
        Cotxe::create([
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABÑ',
            'marca' => 'Vehicle unicode',
        ]);

        $resolucio = Cotxe::resoldreLecturaAcces('1234ACÑ');

        $this->assertSame('tolerada', $resolucio['resultat']);
        $this->assertSame('COTXE01', $resolucio['professor']);
        $this->assertSame('1234ABÑ', $resolucio['matricula_canonica']);
    }

    public function test_reserva_protegix_el_processament_i_la_finestra_posterior(): void
    {
        $service = new CotxeAccessService();

        $this->assertTrue($service->reservarAcces('COTXE01', 'entrada'));
        $this->assertFalse($service->reservarAcces('COTXE01', 'entrada'));
        $this->assertNotNull(DB::table('cotxe_acces_reserves')->value('processing_at'));

        $service->finalitzarReservaAcces('COTXE01', 'entrada', 30);

        $this->assertNull(DB::table('cotxe_acces_reserves')->value('processing_at'));
        $this->assertFalse($service->reservarAcces('COTXE01', 'entrada'));
    }

    public function test_migracio_normalitza_cotxes_i_historial_d_accessos(): void
    {
        $this->insertProfesor('COTXE01');
        DB::table('cotxes')->insert([
            'idProfesor' => 'COTXE01',
            'matricula' => '12-34abc',
            'marca' => 'Vehicle llegat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cotxe_accessos')->insert([
            'matricula' => '12 34-abc',
            'autoritzat' => true,
            'porta_oberta' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_09_25_120000_normalize_cotxe_matricules.php');
        $migration->up();

        $this->assertSame('1234ABC', DB::table('cotxes')->value('matricula'));
        $this->assertSame('1234ABC', DB::table('cotxe_accessos')->value('matricula'));
    }

    public function test_migracio_s_atura_abans_de_crear_duplicats(): void
    {
        $this->insertProfesor('COTXE01');
        DB::table('cotxes')->insert([
            [
                'idProfesor' => 'COTXE01',
                'matricula' => '1234ABC',
                'marca' => 'Vehicle net',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'COTXE01',
                'matricula' => '1234-ABC',
                'marca' => 'Vehicle duplicat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $migration = require database_path('migrations/2026_09_25_120000_normalize_cotxe_matricules.php');

        try {
            $migration->up();
            $this->fail('La migració havia de detectar la col·lisió de matrícules.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('quedarien duplicats', $exception->getMessage());
        }

        $this->assertEqualsCanonicalizing(
            ['1234ABC', '1234-ABC'],
            DB::table('cotxes')->pluck('matricula')->all()
        );
    }

    public function test_professor_pot_tornar_a_afegir_matricula_despres_d_eliminar_la(): void
    {
        $this->insertProfesor('COTXE01');

        $professor = Profesor::on('sqlite')->findOrFail('COTXE01');

        $createResponse = $this
            ->actingAs($professor, 'profesor')
            ->post(route('cotxe.store'), [
                'matricula' => '1234ABC',
                'marca' => 'Vehicle inicial',
            ]);

        $createResponse->assertStatus(302);
        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cotxes', [
            'idProfesor' => 'COTXE01',
            'matricula' => '1234ABC',
            'marca' => 'Vehicle inicial',
        ]);

        $cotxeId = (int) DB::table('cotxes')
            ->where('idProfesor', 'COTXE01')
            ->where('matricula', '1234ABC')
            ->value('id');

        $deleteResponse = $this
            ->actingAs($professor, 'profesor')
            ->get(route('cotxe.delete', ['id' => $cotxeId]));

        $deleteResponse->assertStatus(302);
        $this->assertNull(DB::table('cotxes')->where('id', $cotxeId)->first());

        $recreateResponse = $this
            ->actingAs($professor, 'profesor')
            ->post(route('cotxe.store'), [
                'matricula' => '1234ABC',
                'marca' => 'Vehicle recuperat',
            ]);

        $recreateResponse->assertStatus(302);
        $this->assertSame(
            1,
            DB::table('cotxes')
                ->where('idProfesor', 'COTXE01')
                ->where('matricula', '1234ABC')
                ->count()
        );
        $this->assertSame(
            'Vehicle recuperat',
            DB::table('cotxes')
                ->where('idProfesor', 'COTXE01')
                ->where('matricula', '1234ABC')
                ->value('marca')
        );
    }

    public function test_modal_d_alta_publica_a_store_i_no_a_create(): void
    {
        $this->app->instance('request', Request::create('/cotxe/create', 'GET'));
        View::share('errors', new ViewErrorBag());

        $formulario = new FormBuilder(new Cotxe(), CotxeCrudSchema::FORM_FIELDS);

        $html = $formulario->modal()->render();

        $this->assertStringContainsString('action="http://localhost/cotxe"', $html);
        $this->assertStringContainsString('data-store-url="http://localhost/cotxe"', $html);
        $this->assertStringNotContainsString('action="http://localhost/cotxe/create"', $html);
    }

    public function test_modal_d_alta_de_comissio_publica_a_la_ruta_legacy_create(): void
    {
        $this->app->instance('request', Request::create('/comision/create', 'GET'));
        View::share('errors', new ViewErrorBag());

        $formulario = new FormBuilder(new Cotxe(), CotxeCrudSchema::FORM_FIELDS);

        $html = $formulario->modal()->render();

        $this->assertStringContainsString('action="http://localhost/comision/create"', $html);
        $this->assertStringContainsString('data-store-url="http://localhost/comision/create"', $html);
        $this->assertStringNotContainsString('action="http://localhost/comision"', $html);
    }

    /**
     * Crea l'esquema mínim necessari per al CRUD de cotxes.
     */
    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->date('fecha_baja')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('cotxes', function (Blueprint $table): void {
            $table->id();
            $table->string('matricula', 8);
            $table->string('marca', 80);
            $table->string('idProfesor', 10);
            $table->timestamps();

            $table->unique(['matricula', 'idProfesor']);
        });

        Schema::connection('sqlite')->create('cotxe_accessos', function (Blueprint $table): void {
            $table->id();
            $table->string('matricula');
            $table->string('matricula_canonica')->nullable();
            $table->string('idProfesor', 10)->nullable();
            $table->boolean('autoritzat')->default(false);
            $table->boolean('porta_oberta')->default(false);
            $table->string('device')->nullable();
            $table->string('tipus')->nullable();
            $table->string('resultat_resolucio')->default('sense_candidat');
            $table->unsignedTinyInteger('diferencia_ocr')->nullable();
            $table->unsignedTinyInteger('intents_domotica')->default(0);
            $table->string('error_domotica')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('cotxe_acces_reserves', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor', 10);
            $table->string('tipus');
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->unique(['idProfesor', 'tipus']);
        });

        Schema::connection('sqlite')->create('menus', function (Blueprint $table): void {
            $table->id();
            $table->string('menu')->nullable();
            $table->string('submenu')->nullable();
            $table->string('literal')->nullable();
            $table->string('url')->nullable();
            $table->string('icono')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Dona d'alta un professor autenticable per al test.
     */
    private function insertProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Professor',
            'apellido1' => 'Cotxe',
            'apellido2' => 'Test',
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
