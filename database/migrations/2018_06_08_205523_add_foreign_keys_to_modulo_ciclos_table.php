<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToModuloCiclosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('modulo_ciclos', function(Blueprint $table)
		{
			$table->foreign('idCiclo')->references('id')->on('ciclos')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('idDepartamento')->references('id')->on('departamentos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idModulo')->references('codigo')->on('modulos')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('modulo_ciclos', function(Blueprint $table)
		{
			$table->dropForeign('modulo_ciclos_idciclo_foreign');
			$table->dropForeign('modulo_ciclos_iddepartamento_foreign');
			$table->dropForeign('modulo_ciclos_idmodulo_foreign');
		});
	}

}
