<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->date('fechatramite')->nullable()->after('fecha');
        });
    }

    public function down()
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropColumn('fechatramite');
        });
    }
};
