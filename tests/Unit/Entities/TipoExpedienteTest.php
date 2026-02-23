<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\TipoExpediente;
use Tests\TestCase;

class TipoExpedienteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('expedientes');
        $schema->dropIfExists('tipo_expedientes');

        $schema->create('tipo_expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('titulo')->nullable();
        });

        $schema->create('expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('tipo')->nullable();
        });
    }

    public function test_expedientes_relation_usa_foreign_key_tipo(): void
    {
        DB::table('tipo_expedientes')->insert(['id' => 7, 'titulo' => 'Convivència']);
        DB::table('expedientes')->insert([
            ['id' => 1, 'tipo' => 7],
            ['id' => 2, 'tipo' => 7],
            ['id' => 3, 'tipo' => 9],
        ]);

        $tipo = TipoExpediente::query()->findOrFail(7);

        $this->assertSame(2, $tipo->expedientes()->count());
    }
}

