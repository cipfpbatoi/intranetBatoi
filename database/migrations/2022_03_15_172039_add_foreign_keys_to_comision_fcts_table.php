<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToComisionFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('comision_fcts', function(Blueprint $table)
		{
			$table->foreign('idComision')->references('id')->on('comisiones')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idFct')->references('id')->on('fcts')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('comision_fcts', function(Blueprint $table)
		{
			$table->dropForeign('comision_fcts_idcomision_foreign');
			$table->dropForeign('comision_fcts_idfct_foreign');
		});
	}

}
