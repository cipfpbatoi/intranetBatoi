<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Afig el camp `choices` a les preguntes d'enquesta per permetre
 * respostes tancades amb opcions predefinides.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('options', function (Blueprint $table): void {
            $table->text('choices')->nullable()->after('scala');
        });
    }

    public function down(): void
    {
        Schema::table('options', function (Blueprint $table): void {
            $table->dropColumn('choices');
        });
    }
};
