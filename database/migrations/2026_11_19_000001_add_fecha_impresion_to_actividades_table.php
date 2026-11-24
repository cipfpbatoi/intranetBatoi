<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Conserva en cada activitat la data del llistat d'autorització.
 */
return new class extends Migration {
    /**
     * Afig la data sense inventar valors per a les activitats anteriors.
     */
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table): void {
            $table->date('fecha_impresion')->nullable();
        });
    }

    /**
     * Elimina el camp afegit.
     */
    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table): void {
            $table->dropColumn('fecha_impresion');
        });
    }
};
