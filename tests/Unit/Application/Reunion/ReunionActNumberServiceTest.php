<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Reunion\ReunionActNumberService;
use Intranet\Entities\Profesor;
use Tests\TestCase;

/**
 * Proves de la numeració visible i persistent de les actes.
 */
class ReunionActNumberServiceTest extends TestCase
{
    private ReunionActNumberService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('reunion_acta_counters', function (Blueprint $table): void {
            $table->string('curso', 20);
            $table->string('organo', 100);
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->primary(['curso', 'organo']);
        });
        Schema::create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('curso', 20);
            $table->string('organo_acta', 100);
            $table->unsignedInteger('numero_acta');
            $table->unique(['curso', 'organo_acta', 'numero_acta']);
        });

        $this->service = new ReunionActNumberService();
    }

    public function test_compartix_sequencia_entre_tipus_del_mateix_grup(): void
    {
        $first = $this->next('2026-2027', 2, 'G1');
        $second = $this->next('2026-2027', 7, 'G1');

        $this->assertSame('grup:G1', $first['organo_acta']);
        $this->assertSame(1, $first['numero_acta']);
        $this->assertSame(2, $second['numero_acta']);
    }

    public function test_separa_sequencies_per_curs_i_organ(): void
    {
        $this->next('2026-2027', 2, 'G1');

        $otherGroup = $this->next('2026-2027', 2, 'G2');
        $otherCourse = $this->next('2027-2028', 2, 'G1');

        $this->assertSame(1, $otherGroup['numero_acta']);
        $this->assertSame(1, $otherCourse['numero_acta']);
    }

    public function test_no_reutilitza_numeros_encara_que_s_elimine_l_acta(): void
    {
        $first = $this->next('2026-2027', 2, 'G1');
        DB::table('reuniones')->insert([
            'curso' => '2026-2027',
            'organo_acta' => $first['organo_acta'],
            'numero_acta' => $first['numero_acta'],
        ]);
        DB::table('reuniones')->delete();

        $second = $this->next('2026-2027', 2, 'G1');

        $this->assertSame(2, $second['numero_acta']);
    }

    public function test_resol_els_cinc_organs_convocants(): void
    {
        $creator = $this->creator();

        $this->assertSame('departament:10', $this->next('2026-2027', 0, creator: $creator)['organo_acta']);
        $this->assertSame('claustre', $this->next('2026-2027', 1, creator: $creator)['organo_acta']);
        $this->assertSame('grup:G1', $this->next('2026-2027', 2, 'G1', creator: $creator)['organo_acta']);
        $this->assertSame(
            'grup-treball:GT1',
            $this->next('2026-2027', 3, workGroup: 'GT1', creator: $creator)['organo_acta']
        );
        $this->assertSame('cocope', $this->next('2026-2027', 8, creator: $creator)['organo_acta']);
    }

    public function test_index_unic_impedix_numeros_duplicats(): void
    {
        DB::table('reuniones')->insert([
            'curso' => '2026-2027',
            'organo_acta' => 'grup:G1',
            'numero_acta' => 1,
        ]);

        $this->expectException(QueryException::class);

        DB::table('reuniones')->insert([
            'curso' => '2026-2027',
            'organo_acta' => 'grup:G1',
            'numero_acta' => 1,
        ]);
    }

    /**
     * Reserva un número dins d'una transacció.
     *
     * @return array{organo_acta: string, numero_acta: int}
     */
    private function next(
        string $course,
        int $type,
        ?string $groupCode = null,
        ?string $workGroup = null,
        ?Profesor $creator = null
    ): array {
        return DB::transaction(fn (): array => $this->service->next(
            $course,
            $type,
            $groupCode,
            $workGroup,
            $creator ?? $this->creator()
        ));
    }

    /**
     * Crea el convocant necessari per a resoldre l'òrgan.
     */
    private function creator(): Profesor
    {
        $creator = new Profesor();
        $creator->dni = 'P1';
        $creator->departamento = 10;

        return $creator;
    }
}
