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
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('alumnos');
        parent::tearDown();
    }

    public function test_alumno_profile_update_accepts_heic_photo(): void
    {
        $alumno = $this->createAlumno('10788988');
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

        $alumno->refresh();
        if (!empty($alumno->foto)) {
            $path = storage_path('app/public/fotos/' . $alumno->foto);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    public function test_alumno_profile_update_saves_jpg_photo(): void
    {
        $alumno = $this->createAlumno('10788989');
        $file = UploadedFile::fake()->image('foto.jpg', 200, 200);

        $response = $this->withoutMiddleware([RoleMiddleware::class])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->actingAs($alumno, 'alumno')
            ->put('/alumno/perfil', [
                'email' => 'alumno@example.com',
                'foto' => $file,
            ]);

        $response->assertRedirect('/alumno/home');
        $response->assertSessionHasNoErrors();

        $alumno->refresh();
        $this->assertNotEmpty($alumno->foto);

        $path = storage_path('app/public/fotos/' . $alumno->foto);
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
        $alumno->nia = $nia;
        $alumno->dni = '00000000T';
        $alumno->email = 'alumno@example.com';
        $alumno->password = bcrypt('secret');
        $alumno->save();

        return $alumno;
    }
}
