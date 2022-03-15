<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGruposTrabajoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('grupos_trabajo', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('objetivos', 65535)->nullable();
			$table->string('literal', 40);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('grupos_trabajo');
	}

}
