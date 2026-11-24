<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Guarda la clau externa de les estades FE importades des d'ITACA. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumno_fcts', function (Blueprint $table): void {
            $table->string('itacaImportKey', 80)->nullable()->unique()->after('idSao');
        });
    }

    public function down(): void
    {
        Schema::table('alumno_fcts', function (Blueprint $table): void {
            $table->dropUnique(['itacaImportKey']);
            $table->dropColumn('itacaImportKey');
        });
    }
};
