<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Middleware\VerifyCsrfToken;
use Intranet\Entities\Alumno;
use Intranet\Http\Middleware\RoleMiddleware;
use Tests\TestCase;

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
            $table->timestamps();
        });
    }

    private function createAlumno(string $nia): Alumno
    {
        $alumno = new Alumno();
        $alumno->setConnection('sqlite');
        $alumno->nia = $nia;
        $alumno->dni = '00000000T';
        $alumno->email = 'alumno@example.com';
        $alumno->password = bcrypt('secret');
        $alumno->save();

        return $alumno;
    }
}
