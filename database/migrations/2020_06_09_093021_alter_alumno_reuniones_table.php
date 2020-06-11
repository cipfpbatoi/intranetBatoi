<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAlumnoReunionesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumno_reuniones', function (Blueprint $table) {
            $table->boolean('sent')->default(0);
            $table->string('token',60)->nullable();
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('alumno_reuniones', function (Blueprint $table) {
            $table->dropColumn('sent');
            $table->dropColumn('token');
        });
	}

}
