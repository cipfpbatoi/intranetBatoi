<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Modulo;
use Tests\TestCase;

class ModuloTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('modulos');
        $schema->dropIfExists('horarios');

        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo', 20)->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10)->nullable();
            $table->string('modulo', 20)->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->string('ocupacion', 10)->nullable();
            $table->string('aula', 10)->nullable();
            $table->string('dia_semana', 10)->nullable();
            $table->unsignedInteger('sesion_orden')->nullable();
            $table->unsignedTinyInteger('plantilla')->nullable();
            $table->timestamps();
        });
    }

    public function test_scope_mis_modulos_retalla_duplicats_i_ignora_sense_grup(): void
    {
        DB::table('modulos')->insert([
            ['codigo' => 'M1', 'cliteral' => 'Castella M1', 'vliteral' => 'Valencia M1'],
            ['codigo' => 'M2', 'cliteral' => 'Castella M2', 'vliteral' => 'Valencia M2'],
            ['codigo' => 'M3', 'cliteral' => 'Castella M3', 'vliteral' => 'Valencia M3'],
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P01', 'modulo' => 'M1', 'idGrupo' => 'G1'],
            ['idProfesor' => 'P01', 'modulo' => 'M1', 'idGrupo' => 'G2'],
            ['idProfesor' => 'P01', 'modulo' => 'M2', 'idGrupo' => null],
            ['idProfesor' => 'P02', 'modulo' => 'M3', 'idGrupo' => 'G1'],
        ]);

        $result = Modulo::query()->misModulos('P01')->orderBy('codigo')->pluck('codigo')->all();

        $this->assertSame(['M1'], $result);
    }

    public function test_scope_modulos_grupo_torna_moduls_del_grup(): void
    {
        DB::table('modulos')->insert([
            ['codigo' => 'M1', 'cliteral' => 'Castella M1', 'vliteral' => 'Valencia M1'],
            ['codigo' => 'M2', 'cliteral' => 'Castella M2', 'vliteral' => 'Valencia M2'],
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P01', 'modulo' => 'M1', 'idGrupo' => 'G1'],
            ['idProfesor' => 'P02', 'modulo' => 'M2', 'idGrupo' => 'G2'],
        ]);

        $result = Modulo::query()->modulosGrupo('G1')->pluck('codigo')->all();

        $this->assertSame(['M1'], $result);
    }

    public function test_accessor_literal_respecta_idioma_de_sessio(): void
    {
        DB::table('modulos')->insert([
            'codigo' => 'M1',
            'cliteral' => 'Modulo castellano',
            'vliteral' => 'Mòdul valencià',
        ]);

        $registro = Modulo::query()->findOrFail('M1');

        session()->put('lang', 'es');
        $this->assertSame('Modulo castellano', $registro->literal);

        session()->put('lang', 'ca');
        $this->assertSame('Mòdul valencià', $registro->literal);
    }
}
