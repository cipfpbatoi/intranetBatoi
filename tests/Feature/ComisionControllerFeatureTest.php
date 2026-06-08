<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ComisionControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('comision_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('comision_fcts');
        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('departamentos');
        Schema::connection('sqlite')->dropIfExists('faltas_profesores');
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('menus');
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_confirm_mostra_avis_d_enviament_pendent(): void
    {
        $comisionId = $this->insertComision(0);
        $this->insertProfesor('PC02', 'Profe', config('roles.rol.profesor'));

        $usuario = Profesor::on('sqlite')->findOrFail('PC02');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('comision.confirm', ['comision' => $comisionId]));

        $response->assertOk();
        $response->assertSee('La comissió ha sigut creada, però direcció encara no la veurà.', false);
        $response->assertSee('Enviar a direcció');
        $response->assertSee('Deixar pendent');
    }

    public function test_ruta_unpaid_redirigeix_a_login_si_no_autenticat(): void
    {
        $comisionId = $this->insertComision(3);

        $response = $this->get(route('comision.unpaid', ['comision' => $comisionId]));

        $response->assertStatus(302);
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('/login', $location);
    }

    public function test_ruta_unpaid_denega_rol_no_permes(): void
    {
        $comisionId = $this->insertComision(3);
        $this->insertProfesor('PC01', 'Alumne', config('roles.rol.alumno'));

        $usuario = Profesor::on('sqlite')->findOrFail('PC01');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('comision.unpaid', ['comision' => $comisionId]), ['referer' => '/home']);

        $response->assertStatus(403);
        $response->assertSessionHas('error', 'No estàs autoritzat.');
    }

    public function test_ruta_unpaid_permet_professor_i_actualitza_estat(): void
    {
        $comisionId = $this->insertComision(3);
        $this->insertProfesor('PC02', 'Profe', config('roles.rol.profesor'));

        $usuario = Profesor::on('sqlite')->findOrFail('PC02');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('comision.unpaid', ['comision' => $comisionId]), ['referer' => '/home']);

        $response->assertStatus(302);
        $this->assertSame(4, (int) DB::table('comisiones')->where('id', $comisionId)->value('estado'));
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->unsignedInteger('departamento')->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
                $table->string('sustituye_a', 10)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('departamentos')) {
            Schema::connection('sqlite')->create('departamentos', function (Blueprint $table): void {
                $table->unsignedInteger('id')->primary();
                $table->string('codigo')->nullable();
                $table->string('nombre')->nullable();
            });

            DB::connection('sqlite')->table('departamentos')->insert([
                'id' => 99,
                'codigo' => '99',
                'nombre' => 'Desconegut',
            ]);
        }

        if (!Schema::connection('sqlite')->hasTable('comisiones')) {
            Schema::connection('sqlite')->create('comisiones', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10)->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->unsignedTinyInteger('fct')->default(0);
                $table->text('servicio')->nullable();
                $table->decimal('alojamiento', 8, 2)->default(0);
                $table->decimal('comida', 8, 2)->default(0);
                $table->decimal('gastos', 8, 2)->default(0);
                $table->unsignedInteger('kilometraje')->default(0);
                $table->unsignedTinyInteger('medio')->default(0);
                $table->string('marca')->nullable();
                $table->string('matricula')->nullable();
                $table->text('itinerario')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('fcts')) {
            Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('comision_fcts')) {
            Schema::connection('sqlite')->create('comision_fcts', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idComision')->nullable();
                $table->unsignedBigInteger('idFct')->nullable();
                $table->string('hora_ini')->nullable();
                $table->boolean('aviso')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('faltas_profesores')) {
            Schema::connection('sqlite')->create('faltas_profesores', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10);
                $table->date('dia')->nullable();
                $table->time('entrada')->nullable();
                $table->time('salida')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('activities')) {
            Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('action')->nullable();
                $table->text('comentari')->nullable();
                $table->string('document')->nullable();
                $table->string('model_class')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('author_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('menus')) {
            Schema::connection('sqlite')->create('menus', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('nombre')->nullable();
                $table->string('url')->nullable();
                $table->string('class')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->string('menu')->default('general');
                $table->string('submenu')->default('');
                $table->boolean('activo')->default(true);
                $table->unsignedInteger('orden')->default(0);
                $table->string('img')->nullable();
                $table->string('ajuda')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('notifications')) {
            Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->string('notifiable_type');
                $table->string('notifiable_id');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni, string $nombre, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido1' => 'Test',
            'apellido2' => 'User',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'departamento' => 99,
            'fecha_baja' => null,
            'activo' => 1,
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertComision(int $estado): int
    {
        return (int) DB::table('comisiones')->insertGetId([
            'idProfesor' => 'PC02',
            'desde' => '2026-02-12 09:00:00',
            'hasta' => '2026-02-12 12:00:00',
            'fct' => 0,
            'servicio' => 'Visita',
            'estado' => $estado,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
