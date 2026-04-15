<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Afig un filtre opcional per cicle a les preguntes d'enquesta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('options', function (Blueprint $table): void {
            $table->unsignedInteger('idCiclo')->nullable()->after('choices');
            $table->foreign('idCiclo')->references('id')->on('ciclos')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('options', function (Blueprint $table): void {
            $table->dropForeign(['idCiclo']);
            $table->dropColumn('idCiclo');
        });
    }
};
