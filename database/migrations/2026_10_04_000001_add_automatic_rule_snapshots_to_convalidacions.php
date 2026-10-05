<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Afig l'estat i les instantànies de les convalidacions automàtiques. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->string('regla_automatica_id', 120)->nullable();
            $table->string('regla_automatica_version', 40)->nullable();
            $table->string('regla_automatica_hash', 64)->nullable();
            $table->longText('regla_automatica_snapshot')->nullable();
            $table->longText('evidencia_automatica_snapshot')->nullable();
            $table->longText('base_normativa_automatica')->nullable();
            $table->string('resultat_automatic', 2)->nullable();
            $table->string('mode_nota_automatic', 20)->nullable();
            $table->decimal('nota_resultat_automatic', 5, 2)->nullable();
            $table->index(['regla_automatica_id', 'resultat_automatic'], 'convalidacions_regla_automatica_index');
        });
    }

    /** Retira les instantànies automàtiques. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropIndex('convalidacions_regla_automatica_index');
            $table->dropColumn([
                'regla_automatica_id',
                'regla_automatica_version',
                'regla_automatica_hash',
                'regla_automatica_snapshot',
                'evidencia_automatica_snapshot',
                'base_normativa_automatica',
                'resultat_automatic',
                'mode_nota_automatic',
                'nota_resultat_automatic',
            ]);
        });
    }
};
