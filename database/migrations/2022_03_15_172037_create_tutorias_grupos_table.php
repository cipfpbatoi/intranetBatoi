<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTutoriasGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tutorias_grupos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('idTutoria')->unsigned()->index('tutorias_grupos_idtutoria_foreign');
			$table->string('idGrupo', 5)->index('tutorias_grupos_idgrupo_foreign');
			$table->text('observaciones', 65535);
			$table->date('fecha');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tutorias_grupos');
	}

}
