<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Colaboracion;
use Tests\TestCase;

class ColaboracionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('activities');
        $schema->dropIfExists('colaboraciones');
        $schema->dropIfExists('centros');
        $schema->dropIfExists('ciclos');

        $schema->create('centros', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idEmpresa');
            $table->string('nombre')->nullable();
            $table->string('localidad')->nullable();
            $table->string('horarios')->nullable();
        });

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('ciclo')->nullable();
            $table->unsignedInteger('departamento')->nullable();
        });

        $schema->create('colaboraciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idCentro');
            $table->unsignedInteger('idCiclo');
            $table->string('contacto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('puestos')->default(1);
            $table->string('tutor')->nullable();
            $table->unsignedTinyInteger('estado')->default(1);
            $table->timestamps();
        });

        $schema->create('activities', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('action')->nullable();
            $table->string('model_class')->nullable();
            $table->unsignedInteger('model_id')->nullable();
            $table->text('comentari')->nullable();
            $table->string('document')->nullable();
            $table->string('author_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_scope_empresa_filtra_per_centres_de_lempresa(): void
    {
        DB::table('centros')->insert([
            ['id' => 10, 'idEmpresa' => 1, 'nombre' => 'A'],
            ['id' => 20, 'idEmpresa' => 2, 'nombre' => 'B'],
        ]);

        DB::table('colaboraciones')->insert([
            ['id' => 1, 'idCentro' => 10, 'idCiclo' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'idCentro' => 20, 'idCiclo' => 100, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $ids = Colaboracion::query()->Empresa(1)->pluck('id')->all();

        $this->assertSame([1], $ids);
    }

    public function test_get_anotacio_attribute_concatena_llibreta_de_contactes_ordenada(): void
    {
        DB::table('centros')->insert([
            'id' => 10,
            'idEmpresa' => 1,
            'nombre' => 'A',
        ]);

        DB::table('colaboraciones')->insert([
            'id' => 50,
            'idCentro' => 10,
            'idCiclo' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activities')->insert([
            [
                'action' => 'book',
                'model_class' => 'Intranet\\Entities\\Colaboracion',
                'model_id' => 50,
                'comentari' => 'Primer',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'action' => 'book',
                'model_class' => 'Intranet\\Entities\\Colaboracion',
                'model_id' => 50,
                'comentari' => 'Segon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $colaboracion = Colaboracion::query()->findOrFail(50);

        $this->assertSame("Primer\nSegon", $colaboracion->anotacio);
    }

    public function test_accessors_retorn_sensat_quan_relacions_no_existeixen(): void
    {
        $colaboracion = new Colaboracion([
            'idCentro' => 999,
            'idCiclo' => 999,
            'estado' => 1,
        ]);

        $this->assertSame('', $colaboracion->empresa);
        $this->assertSame('', $colaboracion->Xciclo);
        $this->assertSame('Desconeguda', $colaboracion->localidad);
        $this->assertSame('', $colaboracion->horari);
    }
}

