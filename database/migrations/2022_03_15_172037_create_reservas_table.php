<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReservasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('dia');
			$table->boolean('hora');
			$table->string('idProfesor', 10)->index('reservas_idprofesor_foreign');
			$table->string('idEspacio', 10)->index('reservas_idespacio_foreign');
			$table->string('observaciones', 20)->nullable()->default('');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reservas');
	}

}
