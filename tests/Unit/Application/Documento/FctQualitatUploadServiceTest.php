<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Documento;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Documento\FctQualitatUploadService;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Documento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class FctQualitatUploadServiceTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('adjuntos');
        $schema->dropIfExists('documentos');

        $schema->create('adjuntos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('owner')->nullable();
            $table->string('referencesTo')->nullable();
            $table->string('title');
            $table->unsignedInteger('size')->nullable();
            $table->string('extension', 10);
            $table->string('route');
            $table->timestamps();
        });

        $schema->create('documentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipoDocumento')->nullable();
            $table->string('rol')->nullable();
            $table->string('curso')->nullable();
            $table->string('propietario')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('ciclo')->nullable();
            $table->string('grupo')->nullable();
            $table->text('detalle')->nullable();
            $table->string('enlace')->nullable();
            $table->string('fichero')->nullable();
            $table->string('tags')->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
            $table->string('idDocumento')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory(storage_path('app/public/adjuntos/profesor'));
        $this->deleteDirectory(storage_path('app/gestor'));
        parent::tearDown();
    }

    public function test_create_zip_document_crea_document_i_esborra_adjunts_originals(): void
    {
        $service = new FctQualitatUploadService();
        $profesor = $this->makeProfesor();
        $grupo = $this->makeGrupo();
        $adjunto = $this->createAdjuntoWithFile('profesor/P001', 'entrevista.pdf', 'A1', 'pdf', 'pdf-content');

        $documento = $service->createZipDocument($profesor, $grupo, new Collection([$adjunto]));

        $this->assertInstanceOf(Documento::class, $documento);
        $this->assertNotEmpty($documento->fichero);
        $this->assertFileExists(storage_path('app/' . $documento->fichero));
        $this->assertDatabaseHas('documentos', ['id' => $documento->id, 'tipoDocumento' => 'FCT']);
        $this->assertDatabaseMissing('adjuntos', ['id' => $adjunto->id]);
        $this->assertFileDoesNotExist($this->adjuntoAbsolutePath($adjunto));
    }

    public function test_create_zip_document_retorn_null_si_falta_un_fitxer_i_no_persistix_document(): void
    {
        $service = new FctQualitatUploadService();
        $profesor = $this->makeProfesor();
        $grupo = $this->makeGrupo();
        $adjunto = Adjunto::query()->create([
            'name' => 'entrevista.pdf',
            'owner' => 'P001',
            'title' => 'A1',
            'size' => 10,
            'extension' => 'pdf',
            'route' => 'profesor/P001',
        ]);

        $documento = $service->createZipDocument($profesor, $grupo, new Collection([$adjunto]));

        $this->assertNull($documento);
        $this->assertSame(0, Documento::query()->count());
        $this->assertDatabaseHas('adjuntos', ['id' => $adjunto->id]);
    }

    private function makeProfesor(): Profesor
    {
        $profesor = new Profesor();
        $profesor->dni = 'P001';
        $profesor->nombre = 'Pepa';
        $profesor->apellido1 = 'Garcia';
        $profesor->apellido2 = 'Test';

        return $profesor;
    }

    private function makeGrupo(): Grupo
    {
        $ciclo = new Ciclo();
        $ciclo->ciclo = 'DAM';

        $grupo = new Grupo();
        $grupo->nombre = '1DAM';
        $grupo->setRelation('Ciclo', $ciclo);

        return $grupo;
    }

    private function createAdjuntoWithFile(
        string $route,
        string $name,
        string $title,
        string $extension,
        string $contents
    ): Adjunto {
        $adjunto = Adjunto::query()->create([
            'name' => $name,
            'owner' => 'P001',
            'title' => $title,
            'size' => strlen($contents),
            'extension' => $extension,
            'route' => $route,
        ]);

        $path = $this->adjuntoAbsolutePath($adjunto);
        $this->ensureDirectory(dirname($path));
        file_put_contents($path, $contents);

        return $adjunto;
    }

    private function adjuntoAbsolutePath(Adjunto $adjunto): string
    {
        return storage_path("app/public/adjuntos/{$adjunto->route}/{$adjunto->title}.{$adjunto->extension}");
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path) && !@mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }

    private function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                $this->deleteDirectory($fullPath);
            } else {
                @unlink($fullPath);
            }
        }

        @rmdir($path);
    }
}
