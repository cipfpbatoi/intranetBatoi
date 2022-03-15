<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResultadosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resultados', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('evaluacion');
			$table->boolean('matriculados');
			$table->boolean('evaluados');
			$table->boolean('aprobados');
			$table->text('observaciones', 65535)->nullable();
			$table->string('idProfesor', 10)->index('resultados_idprofesor_foreign');
			$table->boolean('udProg')->nullable();
			$table->boolean('udImp')->nullable();
			$table->integer('idModuloGrupo')->unsigned()->nullable();
			$table->text('adquiridosSI', 65535)->nullable();
			$table->text('adquiridosNO', 65535)->nullable();
			$table->unique(['idModuloGrupo','evaluacion'], 'resultados_idmodulogrupo_evaluacion_unique');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('resultados');
	}

}
