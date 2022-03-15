<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCiclosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ciclos', function(Blueprint $table)
		{
			$table->foreign('departamento')->references('id')->on('departamentos')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ciclos', function(Blueprint $table)
		{
			$table->dropForeign('ciclos_departamento_foreign');
		});
	}

}
