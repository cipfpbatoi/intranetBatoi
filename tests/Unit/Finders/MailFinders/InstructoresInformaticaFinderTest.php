<?php

declare(strict_types=1);

namespace Tests\Unit\Finders\MailFinders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Finders\MailFinders\InstructoresInformaticaFinder;
use Tests\TestCase;

class InstructoresInformaticaFinderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');

        $schema->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('name')->nullable();
            $table->string('surnames')->nullable();
            $table->string('email')->nullable();
        });

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('ciclo')->nullable();
            $table->string('acronim')->nullable();
        });

        $schema->create('colaboraciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idCiclo')->nullable();
        });

        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idColaboracion')->nullable();
            $table->string('idInstructor')->nullable();
            $table->unsignedTinyInteger('asociacion')->default(1);
        });
    }

    protected function tearDown(): void
    {
        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('fcts');
        $schema->dropIfExists('colaboraciones');
        $schema->dropIfExists('ciclos');
        $schema->dropIfExists('instructores');

        parent::tearDown();
    }

    public function test_recupera_nom_es_dels_instructors_amb_fct_de_dam_daw_i_asix(): void
    {
        DB::table('instructores')->insert([
            ['dni' => 'INS-DAM', 'name' => 'Ada', 'surnames' => 'Lovelace', 'email' => 'ada@test.local'],
            ['dni' => 'INS-DAW', 'name' => 'Linus', 'surnames' => 'Torvalds', 'email' => 'linus@test.local'],
            ['dni' => 'INS-ASIX', 'name' => 'Margaret', 'surnames' => 'Hamilton', 'email' => 'margaret@test.local'],
            ['dni' => 'INS-DAM-OLD', 'name' => 'Alan', 'surnames' => 'Kay', 'email' => 'alan@test.local'],
            ['dni' => 'INS-AFI', 'name' => 'Grace', 'surnames' => 'Hopper', 'email' => 'grace@test.local'],
        ]);

        DB::table('ciclos')->insert([
            ['id' => 1, 'ciclo' => 'CFS DAM (LOE)', 'acronim' => 'DAM'],
            ['id' => 2, 'ciclo' => 'CFS DAW (LOE)', 'acronim' => 'DAW'],
            ['id' => 3, 'ciclo' => 'CFS ASIX (LOE)', 'acronim' => null],
            ['id' => 4, 'ciclo' => 'Administració i finances', 'acronim' => 'AFI'],
        ]);

        DB::table('colaboraciones')->insert([
            ['id' => 10, 'idCiclo' => 1],
            ['id' => 20, 'idCiclo' => 2],
            ['id' => 30, 'idCiclo' => 3],
            ['id' => 40, 'idCiclo' => 4],
        ]);

        DB::table('fcts')->insert([
            ['id' => 100, 'idColaboracion' => 10, 'idInstructor' => 'INS-DAM', 'asociacion' => 1],
            ['id' => 200, 'idColaboracion' => 20, 'idInstructor' => 'INS-DAW', 'asociacion' => 1],
            ['id' => 300, 'idColaboracion' => 30, 'idInstructor' => 'INS-ASIX', 'asociacion' => 1],
            ['id' => 350, 'idColaboracion' => 10, 'idInstructor' => 'INS-DAM-OLD', 'asociacion' => 1],
            ['id' => 400, 'idColaboracion' => 40, 'idInstructor' => 'INS-AFI', 'asociacion' => 1],
        ]);

        $finder = new InstructoresInformaticaFinder();

        $this->assertSame(
            ['INS-ASIX', 'INS-DAM-OLD', 'INS-DAM', 'INS-DAW'],
            $finder->getElements()->pluck('dni')->values()->all()
        );
    }
}
