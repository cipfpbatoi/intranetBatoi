<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterProgramacionesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programaciones', function (Blueprint $table) {
            $table->dropForeign('programaciones_idprofesor_foreign');
            $table->dropColumn('idProfesor');
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('programaciones', function (Blueprint $table) {
            $table->string('idProfesor', 10)->index('programaciones_idprofesor_foreign');
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
	}

}
