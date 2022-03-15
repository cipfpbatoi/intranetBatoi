<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActividadesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('actividades', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 75);
			$table->text('descripcion', 65535)->nullable();
			$table->text('objetivos', 65535)->nullable();
			$table->dateTime('desde');
			$table->dateTime('hasta');
			$table->text('comentarios', 65535)->nullable();
			$table->boolean('estado')->default(0);
			$table->timestamps();
			$table->boolean('extraescolar')->default(1);
			$table->boolean('fueraCentro')->default(1);
			$table->integer('idDocumento')->unsigned()->nullable()->index('actividades_iddocumento_foreign');
			$table->boolean('transport')->default(0);
			$table->text('desenvolupament', 65535)->nullable();
			$table->text('valoracio', 65535)->nullable();
			$table->text('aspectes', 65535)->nullable();
			$table->text('dades', 65535)->nullable();
			$table->boolean('recomanada')->default(1);
			$table->boolean('poll')->default(1);
			$table->string('image1', 60)->nullable();
			$table->string('image2', 60)->nullable();
			$table->string('image3', 60)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('actividades');
	}

}
