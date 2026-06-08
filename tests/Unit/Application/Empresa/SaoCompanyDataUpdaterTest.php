<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Empresa;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Empresa\SaoCompanyDataUpdater;
use Intranet\Entities\Centro;
use Intranet\Entities\Empresa;
use Tests\TestCase;

/**
 * Proves de l'actualitzador de dades d'empresa i centre des de SAO.
 */
class SaoCompanyDataUpdaterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Event::fake();

        $this->createSchema();
    }

    public function test_fill_missing_ompli_buits_i_no_sobreescriu(): void
    {
        DB::table('empresas')->insert([
            'id' => 1,
            'cif' => 'B123',
            'nombre' => 'Empresa local',
            'direccion' => '',
            'localidad' => '',
            'telefono' => '',
            'email' => '',
        ]);
        DB::table('centros')->insert([
            'id' => 10,
            'idEmpresa' => 1,
            'nombre' => 'Centre local',
            'direccion' => '',
            'localidad' => '',
            'telefono' => '',
            'email' => '',
        ]);
        $empresa = Empresa::query()->findOrFail(1);
        $centro = Centro::query()->findOrFail(10);

        $result = (new SaoCompanyDataUpdater())->fillMissing($empresa, $centro, [
            'empresa' => [
                'nombre' => 'Empresa SAO',
                'direccion' => 'Carrer SAO 1',
                'localidad' => 'Alcoi',
                'telefono' => '965000000',
                'email' => 'empresa@sao.test',
                'actividad' => 'Serveis',
            ],
            'centre' => [
                'nombre' => 'Centre SAO',
                'direccion' => 'Centre SAO 2',
                'localidad' => 'Alcoi',
                'telefono' => '966000000',
                'email' => 'centre@sao.test',
                'codiPostal' => '03801',
            ],
        ]);

        $this->assertSame(['empresa' => 5, 'centro' => 5], $result);

        $empresa->refresh();
        $centro->refresh();

        $this->assertSame('Empresa local', $empresa->nombre);
        $this->assertSame('Carrer SAO 1', $empresa->direccion);
        $this->assertSame('Serveis', $empresa->actividad);
        $this->assertSame('Centre local', $centro->nombre);
        $this->assertSame('Centre SAO 2', $centro->direccion);
        $this->assertSame('03801', $centro->codiPostal);
    }

    public function test_fill_missing_dry_run_no_persistix(): void
    {
        DB::table('empresas')->insert([
            'id' => 2,
            'cif' => 'B456',
            'nombre' => 'Empresa',
            'direccion' => '',
            'localidad' => '',
            'telefono' => '',
        ]);
        DB::table('centros')->insert([
            'id' => 20,
            'idEmpresa' => 2,
            'nombre' => 'Centre',
            'direccion' => '',
            'localidad' => '',
        ]);
        $empresa = Empresa::query()->findOrFail(2);
        $centro = Centro::query()->findOrFail(20);

        $result = (new SaoCompanyDataUpdater())->fillMissing($empresa, $centro, [
            'empresa' => ['direccion' => 'No persistent'],
            'centre' => ['direccion' => 'No persistent centre'],
        ], true);

        $this->assertSame(['empresa' => 1, 'centro' => 1], $result);
        $this->assertSame('', Empresa::query()->find(2)->direccion);
        $this->assertSame('', Centro::query()->find(20)->direccion);
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('empresas', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('cif')->nullable();
            $table->unsignedInteger('concierto')->nullable();
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('gerente')->nullable();
            $table->string('actividad')->nullable();
            $table->boolean('sao')->default(1);
            $table->string('idSao')->nullable();
            $table->date('data_signatura')->nullable();
            $table->timestamps();
        });

        $schema->create('centros', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idEmpresa')->nullable();
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('horarios')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('idioma')->nullable();
            $table->string('codiPostal')->nullable();
            $table->string('idSao')->nullable();
            $table->timestamps();
        });
    }
}
