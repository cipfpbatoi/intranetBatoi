<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReunionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reuniones', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('tipo')->default(0);
			$table->string('grupo', 6)->nullable();
			$table->string('curso');
			$table->string('numero', 2)->nullable();
			$table->dateTime('fecha');
			$table->string('descripcion', 120);
			$table->text('objetivos', 65535)->nullable();
			$table->string('idProfesor', 10)->index('reuniones_idprofesor_foreign');
			$table->string('idEspacio', 10);
			$table->timestamps();
			$table->boolean('archivada')->default(0);
			$table->string('fichero', 100)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reuniones');
	}

}
