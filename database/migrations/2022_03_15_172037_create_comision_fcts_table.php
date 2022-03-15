<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComisionFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comision_fcts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('idFct')->unsigned();
			$table->integer('idComision')->unsigned()->index('comision_fcts_idcomision_foreign');
			$table->string('hora_ini', 10);
			$table->boolean('aviso');
			$table->unique(['idFct','idComision']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comision_fcts');
	}

}
