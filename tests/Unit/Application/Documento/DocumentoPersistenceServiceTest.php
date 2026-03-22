<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Documento;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Documento\DocumentoPersistenceService;
use Intranet\Entities\Documento;
use Tests\TestCase;

class DocumentoPersistenceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        Schema::create('documentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipoDocumento')->nullable();
            $table->integer('rol')->nullable();
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
            $table->boolean('activo')->default(false);
            $table->integer('idDocumento')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('documentos');
        parent::tearDown();
    }

    public function test_store_from_request_persistix_document_amb_defaults_comuns(): void
    {
        $service = new DocumentoPersistenceService();
        $request = Request::create('/fct/upload', 'POST', [
            'tipoDocumento' => 'FCT',
            'descripcion' => 'Document qualitat',
            'curso' => '2025/2026',
            'propietario' => 'Tutor Proves',
            'supervisor' => 'Tutor Proves',
            'ciclo' => 'DAM',
            'grupo' => '1DAM',
            'detalle' => 'Detall',
            'tags' => 'Fct,Qualitat',
            'activo' => '1',
            'nota' => '4',
        ]);

        $documento = $service->storeFromRequest($request);

        $this->assertInstanceOf(Documento::class, $documento);
        $this->assertNotNull($documento->id);
        $this->assertSame('FCT', $documento->tipoDocumento);
        $this->assertSame('Document qualitat', $documento->descripcion);
        $this->assertSame('2025/2026', $documento->curso);
        $this->assertSame('Tutor Proves', $documento->propietario);
        $this->assertSame('Tutor Proves', $documento->supervisor);
        $this->assertSame('DAM', $documento->ciclo);
        $this->assertSame('1DAM', $documento->grupo);
        $this->assertSame('Detall', $documento->detalle);
        $this->assertSame('Fct,Qualitat', $documento->tags);
        $this->assertSame(1, (int) $documento->activo);
        $this->assertDatabaseHas('documentos', [
            'id' => $documento->id,
            'tipoDocumento' => 'FCT',
            'descripcion' => 'Document qualitat',
        ]);
    }
}
