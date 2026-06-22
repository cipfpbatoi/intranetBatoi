<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Afig la vinculació estructural de les actes de grup al grup docent.
     */
    public function up(): void
    {
        Schema::table('reuniones', function (Blueprint $table): void {
            $table->string('idGrupo', 10)->nullable()->after('grupo')->index();
        });
    }

    /**
     * Elimina la vinculació estructural al grup docent.
     */
    public function down(): void
    {
        Schema::table('reuniones', function (Blueprint $table): void {
            $table->dropIndex(['idGrupo']);
            $table->dropColumn('idGrupo');
        });
    }
};
