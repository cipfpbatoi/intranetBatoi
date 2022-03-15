<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToModuloGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('modulo_grupos', function(Blueprint $table)
		{
			$table->foreign('idGrupo')->references('codigo')->on('grupos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idModuloCiclo')->references('id')->on('modulo_ciclos')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('modulo_grupos', function(Blueprint $table)
		{
			$table->dropForeign('modulo_grupos_idgrupo_foreign');
			$table->dropForeign('modulo_grupos_idmodulociclo_foreign');
		});
	}

}
