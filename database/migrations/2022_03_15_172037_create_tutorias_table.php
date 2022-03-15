<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTutoriasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tutorias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('descripcion', 65535);
			$table->string('fichero', 100)->nullable();
			$table->boolean('obligatoria');
			$table->date('desde');
			$table->date('hasta');
			$table->boolean('grupos')->default(0);
			$table->boolean('tipo')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tutorias');
	}

}
