<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Afig les dades necessàries per a la compatibilitat amb SAO Gestió.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table): void {
            $table->boolean('dependent_gva')->default(false)->after('sao');
        });

        Schema::table('centros', function (Blueprint $table): void {
            $table->double('latitud')->nullable()->after('idSao');
            $table->double('longitud')->nullable()->after('latitud');
        });
    }

    /**
     * Elimina les dades de compatibilitat amb SAO Gestió.
     */
    public function down(): void
    {
        Schema::table('centros', function (Blueprint $table): void {
            $table->dropColumn(['latitud', 'longitud']);
        });

        Schema::table('empresas', function (Blueprint $table): void {
            $table->dropColumn('dependent_gva');
        });
    }
};
