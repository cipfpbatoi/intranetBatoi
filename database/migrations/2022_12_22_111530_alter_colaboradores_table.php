<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColaboradoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colaboradores', function (Blueprint $table) {
            $table->string('name', 80);
            $table->dropForeign('instructor_fcts_idinstructor_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colaboraciones', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->foreign('idInstructor', 'instructor_fcts_idinstructor_foreign')->references('dni')->on('instructores')->onUpdate('CASCADE')->onDelete('NO ACTION');
        });
    }
}
