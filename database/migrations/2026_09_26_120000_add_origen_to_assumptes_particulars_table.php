<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingix les sol·licituds ordinàries de les regularitzacions històriques.
     */
    public function up(): void
    {
        Schema::table('assumptes_particulars', function (Blueprint $table): void {
            $table->enum('origen', ['sollicitud', 'regularitzacio'])
                ->default('sollicitud')
                ->after('estat');
        });
    }

    /**
     * Elimina l'origen de les peticions.
     */
    public function down(): void
    {
        Schema::table('assumptes_particulars', function (Blueprint $table): void {
            $table->dropColumn('origen');
        });
    }
};
