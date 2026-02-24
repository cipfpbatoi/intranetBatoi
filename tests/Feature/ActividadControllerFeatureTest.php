<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ActividadControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('actividad_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('departamentos');
        Schema::connection('sqlite')->dropIfExists('faltas_profesores');
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('menus');
        Schema::connection('sqlite')->dropIfExists('actividad_grupo');
        Schema::connection('sqlite')->dropIfExists('actividad_profesor');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('profesores');
        Schema::connection('sqlite')->dropIfExists('actividades');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_alta_profesor_per_ruta_no_duplica_el_pivot(): void
    {
        $actividadId = $this->insertActividad();
        $this->insertProfesor('P401', 'Anna');
        $this->authenticateAsProfesor('P401');

        DB::table('actividad_profesor')->insert([
            'idActividad' => $actividadId,
            'idProfesor' => 'P401',
            'coordinador' => 1,
        ]);

        $response = $this->withoutMiddleware()->post(
            route('actividad.profesor.store', ['actividad' => $actividadId]),
            ['idProfesor' => 'P401']
        );

        $response->assertRedirect(route('actividad.detalle', ['actividad' => $actividadId]));
        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('idProfesor', 'P401')
            ->count());
    }

    public function test_coordinador_per_ruta_deixa_unic_coordinador(): void
    {
        $actividadId = $this->insertActividad();
        $this->insertProfesor('P501', 'Biel');
        $this->insertProfesor('P502', 'Carla');
        $this->authenticateAsProfesor('P501');

        DB::table('actividad_profesor')->insert([
            ['idActividad' => $actividadId, 'idProfesor' => 'P501', 'coordinador' => 1],
            ['idActividad' => $actividadId, 'idProfesor' => 'P502', 'coordinador' => 0],
        ]);

        $response = $this->withoutMiddleware()->post(
            route('actividad.profesor.coordinador', ['actividad' => $actividadId, 'profesor' => 'P502'])
        );

        $response->assertRedirect(route('actividad.detalle', ['actividad' => $actividadId]));
        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('coordinador', 1)
            ->count());
        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('idProfesor', 'P502')
            ->value('coordinador'));
    }

    public function test_borrar_coordinador_per_ruta_reassigna_a_un_altre(): void
    {
        $actividadId = $this->insertActividad();
        $this->insertProfesor('P601', 'Dani');
        $this->insertProfesor('P602', 'Elena');
        $this->authenticateAsProfesor('P601');

        DB::table('actividad_profesor')->insert([
            ['idActividad' => $actividadId, 'idProfesor' => 'P601', 'coordinador' => 1],
            ['idActividad' => $actividadId, 'idProfesor' => 'P602', 'coordinador' => 0],
        ]);

        $response = $this->withoutMiddleware()->delete(
            route('actividad.profesor.destroy', ['actividad' => $actividadId, 'profesor' => 'P601'])
        );

        $response->assertRedirect(route('actividad.detalle', ['actividad' => $actividadId]));
        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->count());
        $this->assertSame('P602', DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('coordinador', 1)
            ->value('idProfesor'));
    }

    public function test_alta_i_baixa_grup_per_ruta(): void
    {
        $actividadId = $this->insertActividad();
        $this->insertProfesor('P650', 'Irene');
        $this->authenticateAsProfesor('P650');

        DB::table('grupos')->insert([
            'codigo' => 'GX1',
            'nombre' => 'Grup X',
        ]);

        $altaResponse = $this->withoutMiddleware()->post(
            route('actividad.grupo.store', ['actividad' => $actividadId]),
            ['idGrupo' => 'GX1']
        );

        $altaResponse->assertRedirect(route('actividad.detalle', ['actividad' => $actividadId]));
        $this->assertDatabaseHas('actividad_grupo', [
            'idActividad' => $actividadId,
            'idGrupo' => 'GX1',
        ], 'sqlite');

        $baixaResponse = $this->withoutMiddleware()->delete(
            route('actividad.grupo.destroy', ['actividad' => $actividadId, 'grupo' => 'GX1'])
        );

        $baixaResponse->assertRedirect(route('actividad.detalle', ['actividad' => $actividadId]));
        $this->assertDatabaseMissing('actividad_grupo', [
            'idActividad' => $actividadId,
            'idGrupo' => 'GX1',
        ], 'sqlite');
    }

    public function test_ruta_detalle_amb_middleware_redirigeix_a_login_si_no_autenticat(): void
    {
        $actividadId = $this->insertActividad();

        $response = $this->get(route('actividad.detalle', ['actividad' => $actividadId]));

        $response->assertStatus(302);
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('/login', $location);
    }

    public function test_ruta_detalle_amb_middleware_denega_rol_no_permes(): void
    {
        $actividadId = $this->insertActividad();
        $this->insertProfesor('P701', 'Fran', config('roles.rol.alumno'));

        $usuario = Profesor::on('sqlite')->findOrFail('P701');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('actividad.detalle', ['actividad' => $actividadId]), ['referer' => '/home']);

        $response->assertStatus(403);
        $response->assertSessionHas('error', 'No estàs autoritzat.');
    }

    public function test_ruta_notificar_amb_middleware_permet_rol_profesor(): void
    {
        $actividadId = $this->insertActividad();
        $this->insertProfesor('P702', 'Gina', config('roles.rol.profesor'));

        $usuario = Profesor::on('sqlite')->findOrFail('P702');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('actividad.notificar', ['actividad' => $actividadId]), ['referer' => '/home']);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
    }

    public function test_ruta_detalle_renderitza_amb_layout_quan_hi_ha_dades_base(): void
    {
        $actividadId = $this->insertActividad();
        $this->insertProfesor('P703', 'Helena', config('roles.rol.profesor'));
        $this->seedMenusBasics();

        $usuario = Profesor::on('sqlite')->findOrFail('P703');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('actividad.detalle', ['actividad' => $actividadId]));

        $response->assertOk();
        $response->assertSee('Professors:');
        $response->assertSee('Grups:');
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('actividades')) {
            Schema::connection('sqlite')->create('actividades', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->tinyInteger('extraescolar')->default(1);
                $table->tinyInteger('complementaria')->default(1);
                $table->tinyInteger('fueraCentro')->default(0);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('email')->nullable();
                $table->string('foto')->nullable();
                $table->unsignedInteger('departamento')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('grupos')) {
            Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
                $table->string('codigo', 5)->primary();
                $table->string('nombre')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('actividad_profesor')) {
            Schema::connection('sqlite')->create('actividad_profesor', function (Blueprint $table): void {
                $table->unsignedBigInteger('idActividad');
                $table->string('idProfesor', 10);
                $table->boolean('coordinador')->default(false);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('actividad_grupo')) {
            Schema::connection('sqlite')->create('actividad_grupo', function (Blueprint $table): void {
                $table->unsignedBigInteger('idActividad');
                $table->string('idGrupo', 5);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('menus')) {
            Schema::connection('sqlite')->create('menus', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('nombre');
                $table->string('url')->nullable();
                $table->string('class')->nullable();
                $table->integer('rol');
                $table->string('menu');
                $table->string('submenu')->default('');
                $table->boolean('activo')->default(true);
                $table->unsignedInteger('orden')->default(1);
                $table->string('img')->nullable();
                $table->string('ajuda')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('notifications')) {
            Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('type');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->string('notifiable_type');
                $table->string('notifiable_id');
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('faltas_profesores')) {
            Schema::connection('sqlite')->create('faltas_profesores', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10);
                $table->date('dia');
                $table->string('entrada')->nullable();
                $table->string('salida')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('departamentos')) {
            Schema::connection('sqlite')->create('departamentos', function (Blueprint $table): void {
                $table->unsignedInteger('id')->primary();
                $table->string('cliteral')->nullable();
                $table->string('vliteral')->nullable();
            });

            DB::table('departamentos')->insert([
                'id' => 1,
                'cliteral' => 'Informatica',
                'vliteral' => 'Informàtica',
            ]);
        }
    }

    private function insertActividad(): int
    {
        return (int) DB::table('actividades')->insertGetId([
            'name' => 'Activitat test',
            'extraescolar' => 1,
            'complementaria' => 1,
            'fueraCentro' => 0,
            'estado' => 0,
        ]);
    }

    private function insertProfesor(string $dni, string $nombre, ?int $rol = null): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido1' => 'Test',
            'apellido2' => 'Prova',
            'email' => 'test@example.com',
            'foto' => null,
            'departamento' => 1,
            'rol' => $rol ?? config('roles.rol.profesor'),
            'fecha_baja' => null,
            'activo' => 1,
        ]);
    }

    private function seedMenusBasics(): void
    {
        DB::table('menus')->insert([
            [
                'nombre' => 'Inici',
                'url' => '/home',
                'class' => 'fa fa-home',
                'rol' => config('roles.rol.profesor'),
                'menu' => 'general',
                'submenu' => '',
                'activo' => 1,
                'orden' => 1,
                'img' => null,
                'ajuda' => null,
            ],
            [
                'nombre' => 'Perfil',
                'url' => '/perfil',
                'class' => 'fa fa-user',
                'rol' => config('roles.rol.profesor'),
                'menu' => 'topmenu',
                'submenu' => '',
                'activo' => 1,
                'orden' => 1,
                'img' => null,
                'ajuda' => null,
            ],
        ]);
    }

    private function authenticateAsProfesor(string $dni): void
    {
        $profesor = Profesor::on('sqlite')->findOrFail($dni);
        $this->actingAs($profesor, 'profesor');
    }
}
