<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGuardiasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('guardias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idProfesor', 10)->index('guardias_idprofesor_foreign');
			$table->date('dia');
			$table->boolean('hora');
			$table->boolean('realizada');
			$table->text('observaciones', 65535)->nullable();
			$table->text('obs_personal', 65535)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('guardias');
	}

}
