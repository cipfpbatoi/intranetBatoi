<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;
use Tests\TestCase;

class IncidenciaControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('incidencia_controller_feature_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        config(['auth.defaults.guard' => 'profesor']);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Event::fake([PreventAction::class, ActivityReport::class]);

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('incidencias');
        Schema::connection('sqlite')->dropIfExists('tipoincidencias');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_update_denega_quan_no_es_ni_creador_ni_responsable(): void
    {
        $this->insertProfesor('PINC001', config('roles.rol.profesor'));
        $this->insertProfesor('PINC002', config('roles.rol.profesor'));
        $this->insertProfesor('PINC003', config('roles.rol.profesor'));
        $this->insertTipoIncidencia(1, 'PINC003');

        $id = $this->insertIncidencia([
            'tipo' => 1,
            'idProfesor' => 'PINC001',
            'responsable' => 'PINC003',
            'descripcion' => 'Incidència inicial',
            'estado' => 1,
        ]);

        $response = $this
            ->withoutMiddleware()
            ->put('/incidencia/' . $id . '/edit', [
                'tipo' => 1,
                'descripcion' => 'Intent no autoritzat',
                'idProfesor' => 'PINC001',
                'prioridad' => 1,
                'observaciones' => 'Obs',
                'fecha' => '2026-02-21',
            ]);

        $response->assertStatus(403);
        $this->assertSame('Incidència inicial', DB::table('incidencias')->where('id', $id)->value('descripcion'));
    }

    public function test_update_permet_al_creador(): void
    {
        $this->insertProfesor('111A', config('roles.rol.profesor'));
        $this->insertProfesor('PINC011', config('roles.rol.profesor'));
        $this->insertProfesor('PINC012', config('roles.rol.profesor'));
        $this->insertTipoIncidencia(2, 'PINC012');

        $id = $this->insertIncidencia([
            'tipo' => 2,
            'idProfesor' => '111A',
            'responsable' => 'PINC012',
            'descripcion' => 'Abans',
            'estado' => 1,
        ]);

        $response = $this
            ->withoutMiddleware()
            ->put('/incidencia/' . $id . '/edit', [
                'tipo' => 2,
                'descripcion' => 'Descripció actualitzada creador',
                'idProfesor' => '111A',
                'prioridad' => 2,
                'observaciones' => 'Actualitzat',
                'fecha' => '2026-02-21',
            ]);

        $response->assertStatus(302);
        $this->assertSame('Descripció actualitzada creador', DB::table('incidencias')->where('id', $id)->value('descripcion'));
    }

    public function test_update_permet_al_responsable(): void
    {
        $this->insertProfesor('111A', config('roles.rol.profesor'));
        $this->insertProfesor('PINC021', config('roles.rol.profesor'));
        $this->insertProfesor('PINC022', config('roles.rol.profesor'));
        $this->insertTipoIncidencia(3, 'PINC022');

        $id = $this->insertIncidencia([
            'tipo' => 3,
            'idProfesor' => 'PINC021',
            'responsable' => '111A',
            'descripcion' => 'Pendència',
            'estado' => 1,
        ]);

        $response = $this
            ->withoutMiddleware()
            ->put('/incidencia/' . $id . '/edit', [
                'tipo' => 3,
                'descripcion' => 'Actualitzada pel responsable',
                'idProfesor' => 'PINC021',
                'prioridad' => 0,
                'observaciones' => 'OK',
                'fecha' => '2026-02-21',
            ]);

        $response->assertStatus(302);
        $this->assertSame('Actualitzada pel responsable', DB::table('incidencias')->where('id', $id)->value('descripcion'));
    }

    public function test_store_ignora_id_profesor_manipulat_i_usa_l_usuari_autenticat(): void
    {
        $this->insertProfesor('111A', config('roles.rol.profesor'));
        $this->insertProfesor('PINC031', config('roles.rol.profesor'));
        $this->insertProfesor('PINC032', config('roles.rol.profesor'));
        $this->insertTipoIncidencia(4, 'PINC032');

        $response = $this
            ->withoutMiddleware()
            ->post('/incidencia', [
                'tipo' => 4,
                'descripcion' => 'Nova incidència',
                'idProfesor' => 'MANIPULAT',
                'prioridad' => 1,
                'observaciones' => 'Creació test',
                'fecha' => '2026-02-21',
            ]);

        $response->assertStatus(302);

        $incidencia = DB::table('incidencias')->orderByDesc('id')->first();
        $this->assertNotNull($incidencia);
        $this->assertSame('111A', $incidencia->idProfesor);
        $this->assertSame(1, (int) $incidencia->estado);
        $this->assertSame('PINC032', $incidencia->responsable);
    }

    public function test_destroy_denega_quan_no_es_ni_creador_ni_responsable(): void
    {
        $this->insertProfesor('PINC041', config('roles.rol.profesor'));
        $this->insertProfesor('PINC042', config('roles.rol.profesor'));
        $this->insertProfesor('PINC043', config('roles.rol.profesor'));
        $this->insertTipoIncidencia(5, 'PINC043');

        $id = $this->insertIncidencia([
            'tipo' => 5,
            'idProfesor' => 'PINC041',
            'responsable' => 'PINC043',
            'descripcion' => 'No es pot esborrar',
            'estado' => 1,
        ]);

        $response = $this
            ->withoutMiddleware()
            ->get('/incidencia/' . $id . '/delete');

        $response->assertStatus(403);
        $this->assertDatabaseHas('incidencias', ['id' => $id], 'sqlite');
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->integer('codigo')->nullable();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->string('api_token', 80)->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('tipoincidencias')) {
            Schema::connection('sqlite')->create('tipoincidencias', function (Blueprint $table): void {
                $table->unsignedInteger('id')->primary();
                $table->string('nombre')->nullable();
                $table->string('nom')->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->unsignedInteger('tipus')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('incidencias')) {
            Schema::connection('sqlite')->create('incidencias', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('tipo');
                $table->unsignedInteger('espacio')->nullable();
                $table->unsignedInteger('material')->nullable();
                $table->text('descripcion');
                $table->string('idProfesor', 10);
                $table->string('responsable', 10)->nullable();
                $table->unsignedTinyInteger('prioridad')->default(0);
                $table->string('observaciones', 255)->nullable();
                $table->date('fecha')->nullable();
                $table->unsignedTinyInteger('estado')->default(0);
                $table->string('solucion', 255)->nullable();
                $table->date('fechasolucion')->nullable();
                $table->unsignedInteger('orden')->nullable();
                $table->string('imagen')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('activities')) {
            Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('action')->nullable();
                $table->string('model_class')->nullable();
                $table->string('model_id')->nullable();
                $table->text('comentari')->nullable();
                $table->string('document')->nullable();
                $table->string('author_id', 10)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('notifications')) {
            Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('type');
                $table->string('notifiable_type');
                $table->string('notifiable_id');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => random_int(1000, 9999),
            'nombre' => 'Nom' . $dni,
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'api_token' => bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertTipoIncidencia(int $id, string $idProfesor): void
    {
        DB::table('tipoincidencias')->insert([
            'id' => $id,
            'nombre' => 'Tipus ' . $id,
            'nom' => 'Tipus ' . $id,
            'idProfesor' => $idProfesor,
            'tipus' => 0,
        ]);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function insertIncidencia(array $attributes): int
    {
        return (int) DB::table('incidencias')->insertGetId(array_merge([
            'tipo' => 1,
            'espacio' => null,
            'material' => null,
            'descripcion' => 'Incidència',
            'idProfesor' => 'P000000001',
            'responsable' => null,
            'prioridad' => 0,
            'observaciones' => null,
            'fecha' => '2026-02-21',
            'estado' => 0,
            'solucion' => null,
            'fechasolucion' => null,
            'orden' => null,
            'imagen' => null,
        ], $attributes));
    }

}
