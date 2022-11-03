<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAlumnoFctsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumno_fcts', function (Blueprint $table) {
           $table->tinyInteger('realizadas')->default(0);
           $table->tinyInteger('horas_diarias')->default(0);
           $table->date('actualizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumno_fcts', function (Blueprint $table) {
            $table->dropColumn('realizadas');
            $table->dropColumn('horas_diarias');
            $table->dropColumn('actualizacion');
        });
    }
}
