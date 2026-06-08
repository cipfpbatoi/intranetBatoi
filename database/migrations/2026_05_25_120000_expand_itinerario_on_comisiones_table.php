<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Amplia l'itinerari de les comissions per a permetre rutes amb moltes parades.
 */
return new class extends Migration
{
    /**
     * Converteix l'itinerari a text per evitar truncaments en itineraris llargs.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            'ALTER TABLE comisiones MODIFY itinerario TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL'
        );
    }

    /**
     * Restaura el límit anterior de 254 caràcters.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            'ALTER TABLE comisiones MODIFY itinerario VARCHAR(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL'
        );
    }
};
