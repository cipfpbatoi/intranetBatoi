<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Adjunto;
use Intranet\Http\Traits\Core\DropZone;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DropZoneTraitTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('dropzone_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createAdjuntosSchema();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('adjuntos');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_link_retorna_vista_dropzone_amb_dades_esperades(): void
    {
        DummyDropZoneModel::$record = (object) ['quien' => 'Pepet Proves'];
        $controller = new DummyDropZoneController();

        $response = $controller->link(22);

        $this->assertSame('dropzone.index', $response->name());
        $this->assertSame('solicitud', $response->getData()['modelo']);
        $this->assertSame(22, $response->getData()['id']);
        $this->assertSame('Pepet Proves', $response->getData()['quien']);
        $this->assertArrayHasKey('volver', $response->getData()['botones']);
    }

    public function test_link_usa_fallback_fullname_si_no_hi_ha_quien(): void
    {
        DummyDropZoneModel::$record = (object) ['fullName' => 'Nom Complet'];
        $controller = new DummyDropZoneController();

        $response = $controller->link(5);

        $this->assertSame('Nom Complet', $response->getData()['quien']);
    }

    public function test_link_fa_abort_si_falta_model(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("L'atribut 'model' no està definit");

        $controller = new DummyDropZoneWithoutModelController();
        $controller->link(1);
    }

    public function test_link_fa_abort_si_falta_class(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("L'atribut 'class' no està definit");

        $controller = new DummyDropZoneWithoutClassController();
        $controller->link(1);
    }

    public function test_deleteattached_esborra_adjunts_del_path_del_model(): void
    {
        Storage::fake('local');

        $target = Adjunto::create([
            'name' => 'doc.pdf',
            'owner' => '00000000A',
            'referencesTo' => null,
            'title' => 'doc_a',
            'size' => 12,
            'extension' => 'pdf',
            'route' => 'solicitud/7',
        ]);

        $other = Adjunto::create([
            'name' => 'other.pdf',
            'owner' => '00000000A',
            'referencesTo' => null,
            'title' => 'doc_b',
            'size' => 12,
            'extension' => 'pdf',
            'route' => 'solicitud/8',
        ]);

        Storage::put("public/adjuntos/{$target->route}/{$target->title}.{$target->extension}", 'x');
        Storage::put("public/adjuntos/{$other->route}/{$other->title}.{$other->extension}", 'y');

        $controller = new DummyDropZoneController();
        $this->callProtectedMethod($controller, 'deleteAttached', [7]);

        $this->assertDatabaseMissing('adjuntos', ['id' => $target->id]);
        $this->assertDatabaseHas('adjuntos', ['id' => $other->id]);

        Storage::assertMissing("public/adjuntos/{$target->route}/{$target->title}.{$target->extension}");
        Storage::assertExists("public/adjuntos/{$other->route}/{$other->title}.{$other->extension}");
    }

    private function createAdjuntosSchema(): void
    {
        if (Schema::connection('sqlite')->hasTable('adjuntos')) {
            return;
        }

        Schema::connection('sqlite')->create('adjuntos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner')->nullable();
            $table->string('referencesTo')->nullable();
            $table->string('title');
            $table->unsignedBigInteger('size')->nullable();
            $table->string('extension');
            $table->string('route');
            $table->timestamps();
        });
    }
}

class DummyDropZoneModel
{
    public static object $record;

    public static function findOrFail(int $id): object
    {
        return self::$record;
    }
}

class DummyDropZoneController
{
    use DropZone;

    public string $model = 'Solicitud';
    public string $class = DummyDropZoneModel::class;
}

class DummyDropZoneWithoutModelController
{
    use DropZone;

    public string $class = DummyDropZoneModel::class;
}

class DummyDropZoneWithoutClassController
{
    use DropZone;

    public string $model = 'Solicitud';
}
