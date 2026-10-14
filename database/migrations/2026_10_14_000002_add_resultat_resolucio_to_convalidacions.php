<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Afig el resultat comú de les resolucions manuals i automàtiques. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->string('resultat_resolucio', 2)->nullable()->after('nota_resultat_automatic');
            $table->string('mode_nota_resolucio', 20)->nullable()->after('resultat_resolucio');
            $table->decimal('nota_resultat_resolucio', 5, 2)->nullable()->after('mode_nota_resolucio');
        });

        DB::table('convalidacions')
            ->whereNotNull('regla_automatica_id')
            ->update([
                'resultat_resolucio' => DB::raw('resultat_automatic'),
                'mode_nota_resolucio' => DB::raw('mode_nota_automatic'),
                'nota_resultat_resolucio' => DB::raw('nota_resultat_automatic'),
            ]);
    }

    /** Retira els camps comuns de resolució. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn([
                'resultat_resolucio',
                'mode_nota_resolucio',
                'nota_resultat_resolucio',
            ]);
        });
    }
};
