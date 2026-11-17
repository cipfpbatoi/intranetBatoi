<?php

declare(strict_types=1);

namespace Tests\Unit\Application\AlumnoFct;

use InvalidArgumentException;
use Intranet\Application\AlumnoFct\ItacaFctImportRecord;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Proves del contracte normalitzat per a la importació d'FCT des d'ITACA.
 */
class ItacaFctImportRecordTest extends TestCase
{
    #[Test]
    public function normalitza_una_fe_i_calcula_una_clau_determinista(): void
    {
        $record = ItacaFctImportRecord::fromItaca($this->validRecord());

        $this->assertSame('2026-03-01', $record->dataInici);
        $this->assertSame('2026-06-15', $record->dataFi);
        $this->assertTrue($record->autoritzacio);
        $this->assertSame(380, $record->horesPrevistes);
        $this->assertSame(120, $record->horesRealitzades);
        $this->assertStringStartsWith('itaca-fe:', $record->identityKey());
        $this->assertSame($record->identityKey(), ItacaFctImportRecord::fromItaca($this->validRecord())->identityKey());
    }

    #[Test]
    public function la_clau_canvia_quan_canvia_un_component_de_la_fe(): void
    {
        $original = ItacaFctImportRecord::fromItaca($this->validRecord());
        $changed = ItacaFctImportRecord::fromItaca(array_merge($this->validRecord(), [
            'centre_treball' => 'Un altre centre',
        ]));

        $this->assertNotSame($original->identityKey(), $changed->identityKey());
    }

    #[Test]
    public function rebutja_un_registre_sense_nia(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Falta el camp obligatori d'ITACA: nia.");

        ItacaFctImportRecord::fromItaca(array_merge($this->validRecord(), ['nia' => '']));
    }

    #[Test]
    public function rebutja_dates_i_hores_no_valides(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La data d'ITACA no és vàlida: data_inici.");

        ItacaFctImportRecord::fromItaca(array_merge($this->validRecord(), ['data_inici' => '31/02/2026']));
    }

    /**
     * @return array<string, string>
     */
    private function validRecord(): array
    {
        return [
            'nia' => '12345678',
            'cicle' => 'CFGM Prova',
            'centre_treball' => 'Centre de proves',
            'tutor' => 'Tutor de proves',
            'data_inici' => '01/03/2026',
            'data_fi' => '15/06/2026',
            'tipus' => 'FE: GEN',
            'autoritzacio' => 'Sí',
            'confirmacio' => 'Pendent',
            'hores_previstes' => '380',
            'hores_realitzades' => '120',
        ];
    }
}
