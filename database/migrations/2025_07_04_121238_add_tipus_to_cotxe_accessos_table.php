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
        Schema::table('cotxe_accessos', function (Blueprint $table) {
            $table->enum('tipus', ['entrada', 'eixida'])->nullable()->after('porta_oberta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotxe_accessos', function (Blueprint $table) {
            $table->dropColumn('tipus');
        });
    }
};
