<?php

declare(strict_types=1);

namespace Tests\Unit\Seeders;

use Tests\TestCase;

/**
 * Verifica que les dades inicials no incorporen identificadors personals.
 */
class InitialSeedDataTest extends TestCase
{
    /**
     * Comprova que cada DNI de l'SQL inicial és un placeholder vàlid i diferent.
     */
    public function test_initial_sql_uses_distinct_valid_dni_placeholders(): void
    {
        $sql = file_get_contents(public_path('initial.sql'));

        preg_match_all('/\b\d{8}[A-Z]\b/', (string) $sql, $matches);
        $dnis = array_values(array_unique($matches[0]));

        $this->assertSame([
            '00000000T', '00000001R', '00000002W', '00000003A',
            '00000004G', '00000005M', '00000006Y', '00000007F',
            '00000008P', '00000009D', '00000010X', '00000011B',
            '00000012N', '00000013J', '00000014Z', '00000015S',
        ], $dnis);

        foreach ($dnis as $dni) {
            $this->assertTrue($this->hasValidDniLetter($dni));
        }
    }

    /**
     * Comprova que l'usuari inicial conté únicament valors de prova.
     */
    public function test_default_user_seeder_declares_generic_login_data(): void
    {
        $source = file_get_contents(database_path('seeders/DefaultUser.php'));

        $this->assertStringContainsString("'dni' => '00000015S'", (string) $source);
        $this->assertStringContainsString("'nombre' => 'Usuari'", (string) $source);
        $this->assertStringContainsString("'apellido1' => 'De prova'", (string) $source);
        $this->assertStringContainsString("'email' => 'admin.prova@exemple.invalid'", (string) $source);
        $this->assertStringContainsString("bcrypt('Prova2026!')", (string) $source);
        $this->assertStringContainsString("'changePassword' => now()", (string) $source);
    }

    /**
     * Comprova que la configuració d'avisos no conserva el DNI anterior.
     */
    public function test_avisos_migration_uses_a_valid_placeholder_dni(): void
    {
        $source = file_get_contents(database_path(
            'migrations/2026_06_09_120000_add_jefe_estudios_to_avisos_settings.php'
        ));

        $this->assertStringContainsString("'value' => '00000016Q'", (string) $source);
        $this->assertTrue($this->hasValidDniLetter('00000016Q'));
        $this->assertStringNotContainsString('021657327K', (string) $source);
    }

    /**
     * Determina si la lletra final correspon al número DNI.
     */
    private function hasValidDniLetter(string $dni): bool
    {
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $number = (int) substr($dni, 0, 8);

        return $letters[$number % 23] === substr($dni, -1);
    }
}
