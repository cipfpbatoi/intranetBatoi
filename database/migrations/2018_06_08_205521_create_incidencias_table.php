<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIncidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('incidencias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('material')->nullable();
			$table->text('descripcion', 65535);
			$table->boolean('estado');
			$table->string('espacio', 10)->nullable()->index('incidencias_espacio_foreign');
			$table->string('idProfesor', 10)->index('incidencias_idprofesor_foreign');
			$table->string('responsable', 10)->nullable();
			$table->boolean('tipo')->index('incidencias_tipo_foreign');
			$table->boolean('prioridad');
			$table->date('fecha');
			$table->string('Observaciones')->nullable();
			$table->string('solucion')->nullable();
			$table->date('fechasolucion')->nullable();
			$table->integer('orden')->unsigned()->nullable()->index('incidencias_orden_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('incidencias');
	}

}
