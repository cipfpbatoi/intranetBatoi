<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExpedientesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expedientes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idAlumno', 8)->index('expedientes_idalumno_foreign');
			$table->string('idProfesor', 10)->index('expedientes_idprofesor_foreign');
			$table->boolean('tipo')->default(0);
			$table->text('explicacion', 65535);
			$table->date('fecha');
			$table->date('fechasolucion')->nullable();
			$table->boolean('estado')->default(0);
			$table->string('idModulo', 6)->nullable()->index('expedientes_idmodulo_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('expedientes');
	}

}
