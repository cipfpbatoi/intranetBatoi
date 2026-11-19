<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Verifica la recuperació històrica executant la migració real en una BD aïllada.
 */
class ActividadFechaImpresionBackfillTest extends TestCase
{
    /**
     * Prepara únicament els camps necessaris per a la recuperació.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('documentos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->timestamps();
        });
        Schema::create('actividades', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idDocumento')->nullable();
            $table->date('fecha_impresion')->nullable();
            $table->integer('estado');
            $table->timestamps();
        });
    }

    /**
     * Usa created_at del document, no updated_at ni la data actual.
     */
    public function test_recupera_la_data_del_document_per_a_impreses_valorades_i_itaca(): void
    {
        DB::table('documentos')->insert([
            'id' => 10,
            'created_at' => '2025-10-02 16:45:00',
            'updated_at' => '2026-03-01 12:00:00',
        ]);
        foreach ([3, 4, 5] as $estado) {
            $this->insertActividad($estado, 10, $estado);
        }

        $this->migrateDates();

        foreach ([3, 4, 5] as $estado) {
            $actividad = DB::table('actividades')->find($estado);
            $this->assertSame('2025-10-02', $actividad->fecha_impresion);
            $this->assertSame($estado, (int) $actividad->estado);
            $this->assertSame('2026-01-01 09:00:00', $actividad->updated_at);
        }
    }

    /**
     * Respecta les dates ja guardades i permet executar la recuperació de nou.
     */
    public function test_no_sobreescriu_dates_existents_i_es_idempotent(): void
    {
        DB::table('documentos')->insert(['id' => 10, 'created_at' => '2025-10-02 16:45:00']);
        $this->insertActividad(1, 10, 3, '2025-09-15');
        $this->insertActividad(2, 10, 4);

        $this->migrateDates();
        DB::table('documentos')->where('id', 10)->update(['created_at' => '2025-12-01 10:00:00']);
        $this->migrateDates();

        $this->assertSame('2025-09-15', DB::table('actividades')->find(1)->fecha_impresion);
        $this->assertSame('2025-10-02', DB::table('actividades')->find(2)->fecha_impresion);
    }

    /**
     * Sense document vàlid i datat no s'assigna una data fictícia.
     */
    public function test_conserva_null_sense_document_o_sense_data_de_creacio(): void
    {
        DB::table('documentos')->insert(['id' => 10, 'created_at' => null]);
        $this->insertActividad(1, null, 3);
        $this->insertActividad(2, 999, 4);
        $this->insertActividad(3, 10, 5);

        $this->migrateDates();

        $this->assertSame(3, DB::table('actividades')->whereNull('fecha_impresion')->count());
    }

    /**
     * Executa el mateix fitxer de migració que s'aplicarà en desplegar.
     */
    private function migrateDates(): void
    {
        $migration = require database_path('migrations/2026_11_19_000002_backfill_fecha_impresion_from_documentos.php');
        $migration->up();
    }

    /**
     * Crea una activitat amb una marca d'actualització que ha de romandre intacta.
     */
    private function insertActividad(int $id, ?int $documento, int $estado, ?string $fecha = null): void
    {
        DB::table('actividades')->insert([
            'id' => $id,
            'idDocumento' => $documento,
            'estado' => $estado,
            'fecha_impresion' => $fecha,
            'created_at' => '2025-09-01 09:00:00',
            'updated_at' => '2026-01-01 09:00:00',
        ]);
    }
}
