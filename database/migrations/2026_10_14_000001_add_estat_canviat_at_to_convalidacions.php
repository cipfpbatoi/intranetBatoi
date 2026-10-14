<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Afig una data específica per a l'últim canvi d'estat de cada mòdul. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->timestamp('estat_canviat_at')->nullable()->after('revisat_at');
        });

        DB::table('convalidacions')->update([
            'estat_canviat_at' => DB::raw('COALESCE(revisat_at, created_at)'),
        ]);
    }

    /** Retira la data específica afegida a les peticions. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn('estat_canviat_at');
        });
    }
};
