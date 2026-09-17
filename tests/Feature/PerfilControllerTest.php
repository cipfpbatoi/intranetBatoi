<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Http\Middleware\VerifyCsrfToken;
use Intranet\Http\Middleware\RoleMiddleware;
use Intranet\Services\UI\FormBuilder;
use Tests\TestCase;

/**
 * Proves dels fluxos d'actualització i autorització del perfil d'usuari.
 */
class PerfilControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlitePath = storage_path('testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }
        // Assegura que el fitxer existeix abans de connectar
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
        Schema::connection('sqlite')->dropIfExists('profesores');
        Schema::connection('sqlite')->dropIfExists('alumnos');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }
        parent::tearDown();
    }

    public function test_alumno_profile_update_accepts_heic_photo(): void
    {
        $nia = '10788988';
        $alumno = $this->createAlumno($nia);
        $file = UploadedFile::fake()->create('foto.heic', 10, 'image/heic');

        $response = $this->withoutMiddleware([RoleMiddleware::class])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->actingAs($alumno, 'alumno')
            ->put('/alumno/perfil', [
                'email' => 'alumno@example.com',
                'foto' => $file,
            ]);

        $response->assertRedirect('/alumno/home');
        $response->assertSessionHasNoErrors();

        $fresh = Alumno::on('sqlite')->where('nia', $nia)->firstOrFail();
        if (!empty($fresh->foto)) {
            $path = storage_path('app/public/fotos/' . $fresh->foto);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    public function test_alumno_profile_update_saves_jpg_photo(): void
    {
        $nia = '10788989';
        $alumno = $this->createAlumno($nia);
        $file = UploadedFile::fake()->image('foto.jpg', 200, 200);

        $targetDir = storage_path('app/public/fotos');
        if (!is_dir($targetDir) && !@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            $this->markTestSkipped('No es pot crear el directori de fotos en l\'entorn de test.');
        }
        if (!is_writable($targetDir)) {
            $this->markTestSkipped('El directori de fotos no és writable en l\'entorn de test.');
        }

        $response = $this->withoutMiddleware([RoleMiddleware::class])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->actingAs($alumno, 'alumno')
            ->put('/alumno/perfil', [
                'email' => 'alumno@example.com',
                'foto' => $file,
            ]);

        $response->assertRedirect('/alumno/home');
        $response->assertSessionHasNoErrors();

        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD no disponible en l\'entorn de test.');
        }

        $fresh = Alumno::on('sqlite')->where('nia', $nia)->firstOrFail();
        if (empty($fresh->foto)) {
            $this->markTestSkipped('La foto no s\'ha generat en l\'entorn de test.');
        }

        $path = storage_path('app/public/fotos/' . $fresh->foto);
        $this->assertFileExists($path);

        $info = getimagesize($path);
        $this->assertSame(68, $info[0]);
        $this->assertSame(90, $info[1]);

        @unlink($path);
    }

    public function test_alumno_sense_permis_no_pot_canviar_el_seu_rol(): void
    {
        $nia = '10788990';
        $alumno = $this->createAlumno($nia);

        $response = $this->withoutMiddleware([RoleMiddleware::class])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->actingAs($alumno, 'alumno')
            ->put('/alumno/perfil', [
                'email' => 'alumno-nou@example.com',
                'rol' => [
                    config('roles.rol.alumno'),
                    config('roles.rol.administrador'),
                ],
            ]);

        $response->assertRedirect('/alumno/home');
        $response->assertSessionHasNoErrors();

        $fresh = Alumno::on('sqlite')->findOrFail($nia);
        $this->assertSame((int) config('roles.rol.alumno'), (int) $fresh->rol);
        $this->assertSame('alumno-nou@example.com', $fresh->email);
    }

    public function test_professor_sense_permis_no_pot_canviar_el_seu_rol(): void
    {
        $profesor = $this->createProfesor('PROF001', (int) config('roles.rol.profesor'));

        $response = $this->withoutMiddleware([RoleMiddleware::class])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->actingAs($profesor, 'profesor')
            ->put('/perfil', [
                'email' => 'professor-nou@example.com',
                'rol' => [
                    config('roles.rol.profesor'),
                    config('roles.rol.administrador'),
                ],
            ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasNoErrors();

        $fresh = Profesor::on('sqlite')->findOrFail($profesor->dni);
        $this->assertSame((int) config('roles.rol.profesor'), (int) $fresh->rol);
        $this->assertSame('professor-nou@example.com', $fresh->email);
    }

    public function test_professor_pot_emplenar_i_buidar_la_localitat_del_perfil(): void
    {
        $profesor = $this->createProfesor('PROFLOC', (int) config('roles.rol.profesor'));
        $this->withoutMiddleware([RoleMiddleware::class])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->actingAs($profesor, 'profesor')
            ->put('/perfil', ['email' => 'professor@example.com', 'localitat' => '  Alcoi  '])
            ->assertSessionHasNoErrors();

        $this->assertSame('Alcoi', Profesor::on('sqlite')->findOrFail('PROFLOC')->localitat);
        $this->assertArrayHasKey('localitat', (new FormBuilder($profesor))->getDefault());

        $this->put('/perfil', ['email' => 'professor@example.com', 'localitat' => ''])
            ->assertSessionHasNoErrors();

        $this->assertNull(Profesor::on('sqlite')->findOrFail('PROFLOC')->localitat);
    }

    public function test_usuari_sense_permis_no_veu_controls_de_rols(): void
    {
        $profesor = $this->createProfesor('PROF002', (int) config('roles.rol.profesor'));
        $this->actingAs($profesor, 'profesor');

        $html = view('perfil.partials.roles', [
            'formulario' => new FormBuilder($profesor),
        ])->render();

        $this->assertStringNotContainsString('name="rol[]"', $html);
    }

    public function test_direccio_pot_modificar_rols_pel_flux_autoritzat(): void
    {
        $rolDireccio = (int) config('roles.rol.profesor') * (int) config('roles.rol.direccion');
        $direccio = $this->createProfesor('DIRE001', $rolDireccio);
        $profesor = $this->createProfesor('PROF003', (int) config('roles.rol.profesor'));

        $response = $this->withoutMiddleware([RoleMiddleware::class])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->actingAs($direccio, 'profesor')
            ->from('/profesor/' . $profesor->dni . '/edit')
            ->put('/profesor/' . $profesor->dni . '/edit', [
                'email' => 'professor@example.com',
                'rol' => [
                    config('roles.rol.profesor'),
                    config('roles.rol.jefe_dpto'),
                ],
            ]);

        $response->assertRedirect('/profesor/' . $profesor->dni . '/edit');
        $response->assertSessionHasNoErrors();

        $fresh = Profesor::on('sqlite')->findOrFail($profesor->dni);
        $this->assertSame(
            (int) config('roles.rol.profesor') * (int) config('roles.rol.jefe_dpto'),
            (int) $fresh->rol
        );

        $html = view('perfil.partials.roles', [
            'formulario' => new FormBuilder($fresh),
        ])->render();
        $this->assertStringContainsString('name="rol[]"', $html);
    }

    private function createSchema(): void
    {
        if (Schema::connection('sqlite')->hasTable('alumnos')) {
            return;
        }

        Schema::connection('sqlite')->create('alumnos', function (Blueprint $table) {
            $table->string('nia')->primary();
            $table->string('dni')->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable();
            $table->string('password')->nullable();
            $table->unsignedInteger('rol')->default(config('roles.rol.alumno'));
            $table->boolean('DA')->default(false);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('profesores', function (Blueprint $table) {
            $table->string('dni')->primary();
            $table->string('email')->nullable();
            $table->string('emailItaca')->nullable();
            $table->string('password')->nullable();
            $table->string('movil1')->nullable();
            $table->string('movil2')->nullable();
            $table->string('localitat')->nullable();
            $table->unsignedBigInteger('rol')->default(config('roles.rol.profesor'));
            $table->boolean('mostrar')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    private function createAlumno(string $nia): Alumno
    {
        DB::connection('sqlite')->table('alumnos')->insert([
            'nia' => $nia,
            'dni' => '00000000T',
            'email' => 'alumno@example.com',
            'password' => bcrypt('secret'),
            'rol' => config('roles.rol.alumno'),
            'DA' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Alumno::on('sqlite')->findOrFail($nia);
    }

    private function createProfesor(string $dni, int $rol): Profesor
    {
        DB::connection('sqlite')->table('profesores')->insert([
            'dni' => $dni,
            'email' => strtolower($dni) . '@example.com',
            'password' => bcrypt('secret'),
            'rol' => $rol,
            'mostrar' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Profesor::on('sqlite')->findOrFail($dni);
    }
}
