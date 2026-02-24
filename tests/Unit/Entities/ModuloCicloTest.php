<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Modulo_ciclo;
use Tests\TestCase;

class ModuloCicloTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('departamentos');
        $schema->dropIfExists('ciclos');
        $schema->dropIfExists('modulos');
        $schema->dropIfExists('modulo_ciclos');

        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo', 20)->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('ciclo')->nullable();
            $table->string('literal')->nullable();
        });

        $schema->create('departamentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('literal')->nullable();
        });

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo', 20)->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('idDepartamento')->nullable();
            $table->unsignedTinyInteger('curso')->default(1);
        });
    }

    public function test_accessors_x_son_null_safe_quan_relacions_no_existixen(): void
    {
        DB::table('modulo_ciclos')->insert([
            'id' => 1,
            'idModulo' => 'M-NOEXIST',
            'idCiclo' => 9999,
            'idDepartamento' => 9999,
            'curso' => 1,
        ]);

        $mc = Modulo_ciclo::findOrFail(1);

        $this->assertSame('', $mc->Xmodulo);
        $this->assertSame('', $mc->Xciclo);
        $this->assertSame('', $mc->Xdepartamento);
        $this->assertSame('', $mc->Aciclo);
    }
}
