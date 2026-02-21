<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Guardia;
use Intranet\Services\HR\FitxatgeService;
use Tests\TestCase;

class HoraryHelpersTest extends TestCase
{
    private FitxatgeService $fitxatgeService;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('faltas_profesores');
        $schema->dropIfExists('guardias');

        $schema->create('faltas_profesores', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->date('dia');
            $table->time('entrada')->nullable();
            $table->time('salida')->nullable();
            $table->timestamps();
        });

        $schema->create('guardias', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->unsignedTinyInteger('dia');
            $table->unsignedTinyInteger('hora');
            $table->unsignedTinyInteger('realizada')->default(0);
            $table->string('observaciones')->nullable();
            $table->string('obs_personal')->nullable();
            $table->timestamps();
        });

        $this->fitxatgeService = app(FitxatgeService::class);
    }

    public function test_estadentro_torna_true_si_ultima_fitxa_no_te_salida(): void
    {
        DB::connection('sqlite')->table('faltas_profesores')->insert([
            'idProfesor' => 'P001',
            'dia' => date('Y-m-d'),
            'entrada' => '08:00:00',
            'salida' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue($this->fitxatgeService->isInside('P001', false));
    }

    public function test_estadentro_torna_false_si_no_hi_ha_fitxes_o_esta_eixit(): void
    {
        $this->assertFalse($this->fitxatgeService->isInside('P002', false));

        DB::connection('sqlite')->table('faltas_profesores')->insert([
            'idProfesor' => 'P002',
            'dia' => date('Y-m-d'),
            'entrada' => '08:00:00',
            'salida' => '09:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertFalse($this->fitxatgeService->isInside('P002', false));
    }

    public function test_entrada_i_salida_lixen_ultim_fichatge_de_sessio(): void
    {
        session([
            'ultimoFichaje' => (object) [
                'entrada' => '08:12:00',
                'salida' => '14:34:56',
            ],
        ]);

        $this->assertSame('08:12', $this->fitxatgeService->sessionEntry());
        $this->assertSame('14:34', $this->fitxatgeService->sessionExit());
    }

    public function test_estainstituto_evalua_interval_obert_per_lhora_de_salida(): void
    {
        DB::connection('sqlite')->table('faltas_profesores')->insert([
            'idProfesor' => 'P003',
            'dia' => '2026-02-12',
            'entrada' => '08:00:00',
            'salida' => '10:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue($this->fitxatgeService->wasInsideAt('P003', '2026-02-12', '09:00:00'));
        $this->assertFalse($this->fitxatgeService->wasInsideAt('P003', '2026-02-12', '10:00:00'));
        $this->assertFalse($this->fitxatgeService->wasInsideAt('P003', '2026-02-12', '07:59:59'));
    }

    public function test_estaguardia_i_profesoresguardia(): void
    {
        DB::connection('sqlite')->table('guardias')->insert([
            [
                'idProfesor' => 'P010',
                'dia' => 2,
                'hora' => 3,
                'realizada' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'P011',
                'dia' => 2,
                'hora' => 3,
                'realizada' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->assertTrue(
            Guardia::query()->Profesor('P010')->DiaHora(2, 3)->exists()
        );
        $this->assertFalse(
            Guardia::query()->Profesor('P010')->DiaHora(2, 4)->exists()
        );

        $ids = Guardia::query()
            ->DiaHora(2, 3)
            ->select('idProfesor')
            ->pluck('idProfesor')
            ->all();
        sort($ids);
        $this->assertSame(['P010', 'P011'], $ids);
    }
}
