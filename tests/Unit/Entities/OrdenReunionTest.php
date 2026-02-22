<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\OrdenReunion;
use Tests\TestCase;

class OrdenReunionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('ordenes_reuniones');

        $schema->create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->unsignedInteger('orden');
            $table->string('descripcion')->nullable();
            $table->text('resumen')->nullable();
        });
    }

    public function test_helpers_busquen_per_reunio_i_ordre_i_no_peten_si_no_existix(): void
    {
        DB::table('ordenes_reuniones')->insert([
            ['id' => 1, 'idReunion' => 10, 'orden' => 1, 'descripcion' => 'Obs', 'resumen' => 'Resum 1'],
            ['id' => 2, 'idReunion' => 10, 'orden' => 2, 'descripcion' => 'Proj', 'resumen' => 'Resum 2'],
            ['id' => 3, 'idReunion' => 20, 'orden' => 1, 'descripcion' => 'Obs', 'resumen' => 'Altre resum'],
        ]);

        $this->assertSame('Resum 1', OrdenReunion::resumenByReunionAndOrder(10, 1));
        $this->assertSame('Resum 2', OrdenReunion::resumenByReunionAndOrder(10, 2));
        $this->assertSame('', OrdenReunion::resumenByReunionAndOrder(10, 3));
        $this->assertSame(1, OrdenReunion::firstByReunionAndOrder(10, 1)?->id);
        $this->assertNull(OrdenReunion::firstByReunionAndOrder(99, 1));
    }
}

