<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Ocupacion;
use Tests\TestCase;

class OcupacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('horarios');
        $schema->dropIfExists('ocupaciones');

        $schema->create('ocupaciones', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('nom')->nullable();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('ocupacion', 10)->nullable();
            $table->timestamps();
        });
    }

    public function test_horarios_relation_usa_claus_correctes(): void
    {
        DB::table('ocupaciones')->insert([
            ['codigo' => 'GUA', 'nombre' => 'Guardia', 'nom' => 'Guàrdia'],
            ['codigo' => 'BIB', 'nombre' => 'Biblioteca', 'nom' => 'Biblioteca'],
        ]);

        DB::table('horarios')->insert([
            ['ocupacion' => 'GUA'],
            ['ocupacion' => 'GUA'],
            ['ocupacion' => 'BIB'],
        ]);

        $ocupacion = Ocupacion::query()->findOrFail('GUA');

        $this->assertCount(2, $ocupacion->Horarios);
        $this->assertCount(2, $ocupacion->Ocupacion);
    }

    public function test_literal_accessor_respecta_idioma_i_fallback(): void
    {
        DB::table('ocupaciones')->insert([
            ['codigo' => 'A', 'nombre' => 'Castella', 'nom' => 'Valencià'],
            ['codigo' => 'B', 'nombre' => null, 'nom' => 'Només valencià'],
            ['codigo' => 'C', 'nombre' => 'Solo castellano', 'nom' => null],
        ]);

        session()->put('lang', 'es');
        $this->assertSame('Castella', Ocupacion::query()->findOrFail('A')->literal);
        $this->assertSame('Només valencià', Ocupacion::query()->findOrFail('B')->literal);

        session()->put('lang', 'ca');
        $this->assertSame('Valencià', Ocupacion::query()->findOrFail('A')->literal);
        $this->assertSame('Solo castellano', Ocupacion::query()->findOrFail('C')->literal);
    }
}

