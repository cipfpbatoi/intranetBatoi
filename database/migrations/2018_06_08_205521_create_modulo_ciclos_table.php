<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModuloCiclosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modulo_ciclos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idModulo', 6)->index('modulo_ciclos_idmodulo_index');
			$table->integer('idCiclo')->index('modulo_ciclos_idciclo_foreign');
			$table->string('curso', 1);
			$table->string('enlace', 200)->nullable();
			$table->boolean('idDepartamento')->nullable()->index('modulo_ciclos_iddepartamento_foreign');
			$table->unique(['idModulo','idCiclo'], 'modulo_ciclos_idmodulo_idciclo_unique');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modulo_ciclos');
	}

}
