<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Afig la ruta del document oficial firmat de la resolució.
     */
    public function up(): void
    {
        Schema::table('assumptes_particulars', function (Blueprint $table): void {
            $table->string('resolucio_document')->nullable()->after('falta_id');
        });
    }

    /**
     * Elimina la ruta del document oficial firmat.
     */
    public function down(): void
    {
        Schema::table('assumptes_particulars', function (Blueprint $table): void {
            $table->dropColumn('resolucio_document');
        });
    }
};
