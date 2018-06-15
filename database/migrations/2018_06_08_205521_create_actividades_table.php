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
                        $table->boolean('extraescolar')->default(1);
			$table->boolean('fueraCentro')->default(1);
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
		Schema::drop('actividades');
	}

}
