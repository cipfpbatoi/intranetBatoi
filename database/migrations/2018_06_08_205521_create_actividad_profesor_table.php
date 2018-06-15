<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActividadProfesorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('actividad_profesor', function(Blueprint $table)
		{
			$table->integer('idActividad')->unsigned();
			$table->string('idProfesor', 10)->index('actividad_profesor_idprofesor_foreign');
			$table->boolean('coordinador')->default(0);
			$table->primary(['idActividad','idProfesor']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('actividad_profesor');
	}

}
