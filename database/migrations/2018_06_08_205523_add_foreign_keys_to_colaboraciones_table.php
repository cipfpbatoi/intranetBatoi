<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToColaboracionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('colaboraciones', function(Blueprint $table)
		{
			$table->foreign('idCentro', 'colaboraciones_idCentro_foreign')->references('id')->on('centros')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idCiclo', 'colaboraciones_idCiclo_foreign')->references('id')->on('ciclos')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('colaboraciones', function(Blueprint $table)
		{
			$table->dropForeign('colaboraciones_idCentro_foreign');
			$table->dropForeign('colaboraciones_idCiclo_foreign');
		});
	}

}
