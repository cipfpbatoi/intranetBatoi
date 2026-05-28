<?php

declare(strict_types=1);

namespace Tests\Unit\Entities\Poll;

use Intranet\Entities\Poll\Option;
use Tests\TestCase;

/**
 * Proves unitàries de normalització d'opcions de resposta.
 */
class OptionTest extends TestCase
{
    public function test_choice_values_admet_formats_antics_en_una_linia(): void
    {
        $option = new Option();
        $option->choices = 'DAM1 | DAM2 | DAM3';

        $this->assertSame(['DAM1', 'DAM2', 'DAM3'], $option->choice_values);
    }

    public function test_choice_values_mante_suport_per_salts_de_linia(): void
    {
        $option = new Option();
        $option->choices = "Optativa 1\nOptativa 2\nOptativa 3";

        $this->assertSame(['Optativa 1', 'Optativa 2', 'Optativa 3'], $option->choice_values);
    }
}
