<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdenesTrabajoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ordenes_trabajo', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('descripcion');
			$table->boolean('estado')->default(0);
			$table->string('idProfesor', 10)->nullable()->index('ordenes_trabajo_idprofesor_foreign');
			$table->boolean('tipo')->default(0);
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
		Schema::drop('ordenes_trabajo');
	}

}
