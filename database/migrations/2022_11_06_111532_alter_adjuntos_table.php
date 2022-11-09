<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdjuntosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjuntos', function (Blueprint $table) {
            $table->string('referencesTo', 200)->nullable()->change();
            $table->string('route', 60)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adjuntos', function (Blueprint $table) {
            $table->string('referencesTo', 12)->nullable()->change();
            $table->string('route', 60)->change();
        });
    }
}
