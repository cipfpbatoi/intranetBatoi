<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class HorarioControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('horario_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('menus');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_horario_propuestas_redirigeix_a_login_si_no_autenticat(): void
    {
        $response = $this->get(route('horario.propuestas'));

        $response->assertStatus(302);
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('/login', $location);
    }

    public function test_horario_aceptar_aplica_canvis_i_guardat_fitxer(): void
    {
        Storage::fake('local');

        $this->insertProfesor('PD01', config('roles.rol.profesor'));
        DB::table('horarios')->insert([
            'idProfesor' => 'PD01',
            'dia_semana' => 'L',
            'sesion_orden' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::disk('local')->put('/horarios/PD01.json', json_encode([
            'estado' => 'Aceptado',
            'cambios' => [
                ['de' => '1-L', 'a' => '2-M'],
            ],
        ]));

        $usuario = Profesor::on('sqlite')->findOrFail('PD01');

        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('horario.aceptar', ['profesor' => 'PD01']));

        $response->assertStatus(302);
        $this->assertSame(2, (int) DB::table('horarios')->where('idProfesor', 'PD01')->value('sesion_orden'));
        $this->assertSame('M', (string) DB::table('horarios')->where('idProfesor', 'PD01')->value('dia_semana'));

        $saved = json_decode((string) Storage::disk('local')->get('/horarios/PD01.json'), true);
        $this->assertSame('Guardado', $saved['estado'] ?? null);
        $this->assertSame([], $saved['cambios'] ?? null);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('notifications')) {
            Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('type');
                $table->string('notifiable_type');
                $table->string('notifiable_id');
                $table->text('data');
                $table->dateTime('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('menus')) {
            Schema::connection('sqlite')->create('menus', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('nombre')->nullable();
                $table->string('url')->nullable();
                $table->string('class')->nullable();
                $table->unsignedInteger('rol')->default(1);
                $table->string('menu')->nullable();
                $table->string('submenu')->nullable();
                $table->unsignedTinyInteger('activo')->default(1);
                $table->unsignedInteger('orden')->default(1);
                $table->string('img')->nullable();
                $table->string('ajuda')->nullable();
            });
        }

        if (Schema::connection('sqlite')->hasTable('profesores')) {
            // continuem per assegurar horaris
        } else {
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
        }

        if (!Schema::connection('sqlite')->hasTable('horarios')) {
            Schema::connection('sqlite')->create('horarios', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10);
                $table->string('dia_semana')->nullable();
                $table->unsignedInteger('sesion_orden')->nullable();
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
