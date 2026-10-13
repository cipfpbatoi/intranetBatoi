<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Services\HR\FitxatgeService;
use Tests\TestCase;

class FitxatgeServiceTest extends TestCase
{
    use WithoutModelEvents;

    private ?string $previousRemoteAddr = null;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        $this->previousRemoteAddr = $_SERVER['REMOTE_ADDR'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousRemoteAddr === null) {
            unset($_SERVER['REMOTE_ADDR']);
        } else {
            $_SERVER['REMOTE_ADDR'] = $this->previousRemoteAddr;
        }

        parent::tearDown();
    }

    public function test_fitxar_crea_entrada_quan_no_hi_ha_fitxatge_previ(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.2';

        $fitxatge = app(FitxatgeService::class)->fitxar('P100');

        $this->assertNotFalse($fitxatge);
        $this->assertNotNull($fitxatge);
        $this->assertSame('P100', (string) $fitxatge->idProfesor);
        $this->assertNull($fitxatge->salida);
    }

    public function test_fitxar_tanca_eixida_quan_hi_ha_entrada_oberta(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.2';

        DB::table('faltas_profesores')->insert([
            'idProfesor' => 'P101',
            'dia' => date('Y-m-d'),
            'entrada' => now()->subMinutes(20)->format('H:i:s'),
            'salida' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $fitxatge = app(FitxatgeService::class)->fitxar('P101');

        $this->assertNotNull($fitxatge);
        $this->assertNotNull($fitxatge->salida);
    }

    public function test_fitxar_retornar_null_si_ultim_es_menys_de_10_minuts(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.2';

        DB::table('faltas_profesores')->insert([
            'idProfesor' => 'P102',
            'dia' => date('Y-m-d'),
            'entrada' => now()->subMinutes(5)->format('H:i:s'),
            'salida' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = app(FitxatgeService::class)->fitxar('P102');

        $this->assertNull($result);
    }

    public function test_fitxar_retornar_false_si_ip_no_es_privada(): void
    {
        $_SERVER['REMOTE_ADDR'] = '8.8.8.8';

        $result = app(FitxatgeService::class)->fitxar('P103');

        $this->assertFalse($result);
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('faltas_profesores', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->date('dia');
            $table->time('entrada');
            $table->time('salida')->nullable();
            $table->timestamps();
        });
    }
}

