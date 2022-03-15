<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFaltasProfesoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('faltas_profesores', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idProfesor', 10)->index('faltas_profesores_idprofesor_foreign');
			$table->date('dia');
			$table->time('entrada')->nullable();
			$table->time('salida')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('faltas_profesores');
	}

}
