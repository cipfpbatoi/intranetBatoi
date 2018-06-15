<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFaltasItacaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('faltas_itaca', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idProfesor', 10)->index('faltas_itaca_idprofesor_foreign');
			$table->date('dia');
			$table->boolean('sesion_orden');
			$table->boolean('estado')->default(0);
			$table->boolean('enCentro')->default(0);
			$table->string('idGrupo', 5)->index('faltas_itaca_idgrupo_foreign');
			$table->text('justificacion', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('faltas_itaca');
	}

}
