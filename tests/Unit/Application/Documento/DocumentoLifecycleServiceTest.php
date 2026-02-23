<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Documento;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Documento\DocumentoLifecycleService;
use Intranet\Entities\Documento;
use Tests\TestCase;

class DocumentoLifecycleServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('documentos');

        $schema->create('documentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipoDocumento')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('fichero')->nullable();
            $table->string('idDocumento')->nullable();
            $table->timestamps();
        });
    }

    public function test_delete_esborra_fitxer_fisic_i_registre_quan_toca(): void
    {
        $relativeFile = 'testing/documento_lifecycle/delete_me.pdf';
        $absoluteFile = storage_path('app/' . $relativeFile);
        $this->ensureDirectory(dirname($absoluteFile));
        file_put_contents($absoluteFile, 'dummy');

        $id = (int) DB::table('documentos')->insertGetId([
            'tipoDocumento' => 'TipusInexistent',
            'descripcion' => 'Doc test',
            'fichero' => $relativeFile,
            'idDocumento' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $documento = Documento::query()->findOrFail($id);
        $service = new DocumentoLifecycleService();

        $result = $service->delete($documento);

        $this->assertTrue($result);
        $this->assertFalse(file_exists($absoluteFile));
        $this->assertNull(Documento::query()->find($id));
    }

    public function test_delete_esborra_registre_encara_que_no_hi_haja_fitxer(): void
    {
        $id = (int) DB::table('documentos')->insertGetId([
            'tipoDocumento' => 'Fichero',
            'descripcion' => 'Doc sense fitxer',
            'fichero' => null,
            'idDocumento' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $documento = Documento::query()->findOrFail($id);
        $service = new DocumentoLifecycleService();

        $result = $service->delete($documento);

        $this->assertTrue($result);
        $this->assertNull(Documento::query()->find($id));
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path) && !@mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
}
