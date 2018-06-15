<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdenesReunionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ordenes_reuniones', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('idReunion')->unsigned()->index('ordenes_reuniones_idreunion_foreign');
			$table->boolean('tarea')->default(0);
			$table->string('idProfesor', 10)->nullable();
			$table->boolean('realizada')->default(0);
			$table->boolean('orden')->default(1);
			$table->string('descripcion', 120);
			$table->text('resumen', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ordenes_reuniones');
	}

}
