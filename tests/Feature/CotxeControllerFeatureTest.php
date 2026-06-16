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
use Intranet\Presentation\Crud\CotxeCrudSchema;
use Intranet\Services\UI\FormBuilder;
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

        $this->sqlitePath = storage_path('cotxe_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('cotxes');
        Schema::connection('sqlite')->dropIfExists('menus');
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
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
