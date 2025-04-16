<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tutorias_grupos', function (Blueprint $table) {
            $table->string('idGrupo', 10)->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutorias_grupos', function (Blueprint $table) {
            $table->string('idGrupo', 5)->change();

        });
    }
};
