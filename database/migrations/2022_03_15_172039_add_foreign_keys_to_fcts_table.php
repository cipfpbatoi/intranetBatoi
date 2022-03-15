<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('fcts', function(Blueprint $table)
		{
			$table->foreign('idColaboracion')->references('id')->on('colaboraciones')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idInstructor')->references('dni')->on('instructores')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('fcts', function(Blueprint $table)
		{
			$table->dropForeign('fcts_idcolaboracion_foreign');
			$table->dropForeign('fcts_idinstructor_foreign');
		});
	}

}
