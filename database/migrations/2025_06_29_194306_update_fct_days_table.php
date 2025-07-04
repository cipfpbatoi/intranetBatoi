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
        Schema::table('fct_days', function (Blueprint $table) {
            $table->dropForeign(['alumno_fct_id']);
            $table->dropColumn('alumno_fct_id');

            $table->string('nia', 8)->after('id');
            $table->unsignedInteger('colaboracion_id')->after('nia')->nullable();
            $table->foreign('colaboracion_id')->references('id')->on('colaboraciones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('fct_days', function (Blueprint $table) {
            $table->dropForeign(['colaboracion_id']);
            $table->dropColumn(['nia', 'colaboracion_id']);

            $table->unsignedInteger('alumno_fct_id');
            $table->foreign('alumno_fct_id')->references('id')->on('alumno_fct')->onDelete('cascade');
        });
    }

};
