<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class EspacioControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->sqlitePath = storage_path('espacio_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('espacios');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_store_retorna_error_validacio_si_l_aula_ja_existeix(): void
    {
        $this->insertProfesor('DIR01', config('roles.rol.profesor') * config('roles.rol.direccion'));

        DB::table('espacios')->insert([
            'aula' => 'E amable',
            'descripcion' => 'Sala reunió',
            'idDepartamento' => 99,
            'gMati' => null,
            'gVesprada' => null,
            'reservable' => 0,
        ]);

        $user = Profesor::on('sqlite')->findOrFail('DIR01');
        $response = $this
            ->actingAs($user, 'profesor')
            ->post('/espacio/create', [
                'aula' => 'E amable',
                'descripcion' => 'Sala reunió (alumnes, pares, orientació)',
                'idDepartamento' => 99,
                'gMati' => null,
                'gVesprada' => null,
                'reservable' => 0,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('aula');

        $this->assertSame(1, DB::table('espacios')->where('aula', 'E amable')->count());
    }

    public function test_update_permet_mantindre_la_mateixa_aula(): void
    {
        $this->insertProfesor('DIR02', config('roles.rol.profesor') * config('roles.rol.direccion'));

        DB::table('espacios')->insert([
            'aula' => 'E amable',
            'descripcion' => 'Sala reunió',
            'idDepartamento' => 99,
            'gMati' => null,
            'gVesprada' => null,
            'reservable' => 0,
        ]);

        $user = Profesor::on('sqlite')->findOrFail('DIR02');
        $response = $this
            ->actingAs($user, 'profesor')
            ->put('/espacio/E%20amable/edit', [
                'aula' => 'E amable',
                'descripcion' => 'Sala reunió actualitzada',
                'idDepartamento' => 99,
                'gMati' => null,
                'gVesprada' => null,
                'reservable' => 1,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $registro = DB::table('espacios')->where('aula', 'E amable')->first();
        $this->assertNotNull($registro);
        $this->assertSame('Sala reunió actualitzada', $registro->descripcion);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->unsignedInteger('rol')->default(3);
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('espacios')) {
            Schema::connection('sqlite')->create('espacios', function (Blueprint $table): void {
                $table->string('aula', 10)->primary();
                $table->string('descripcion', 100);
                $table->unsignedTinyInteger('idDepartamento');
                $table->string('gMati', 5)->nullable();
                $table->string('gVesprada', 5)->nullable();
                $table->boolean('reservable')->default(false);
            });
        }
    }

    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'rol' => $rol,
            'activo' => 1,
        ]);
    }
}
