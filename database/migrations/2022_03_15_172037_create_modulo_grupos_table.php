<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModuloGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modulo_grupos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('idModuloCiclo')->unsigned();
			$table->string('idGrupo', 5)->index('modulo_grupos_idgrupo_foreign');
			$table->unique(['idModuloCiclo','idGrupo'], 'modulo_grupos_idmodulociclo_idgrupo_unique');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modulo_grupos');
	}

}
