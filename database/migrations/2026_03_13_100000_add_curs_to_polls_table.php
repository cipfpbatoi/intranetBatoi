<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Afegeix la columna `curs` a la taula `polls` per filtrar
 * enquestes per any del cicle formatiu (1r o 2n).
 * NULL indica que l'enquesta és visible per a tots els cursos.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table): void {
            $table->unsignedTinyInteger('curs')->nullable()->after('hasta');
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table): void {
            $table->dropColumn('curs');
        });
    }
};
