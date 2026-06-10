<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Amplia l'enllaç de les tasques per acceptar URLs llargues de serveis externs.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE tasks MODIFY enlace TEXT NULL');
    }

    /**
     * Restaura la mida anterior de l'enllaç de les tasques.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE tasks MODIFY enlace VARCHAR(200) NULL');
    }
};
