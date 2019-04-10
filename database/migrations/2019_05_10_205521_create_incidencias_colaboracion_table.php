<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIncidenciasColaboracionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('incidencias_colaboracion', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('idColaboracion')->unsigned()->index();
            $table->string('dni', 10)->collation('utf8_unicode_ci')->nullable()->index();
            $table->tinyInteger('tipo');
            $table->text('observaciones', 65535);
            $table->foreign('dni')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('idColaboracion')->references('id')->on('colaboraciones')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('incidencias_colaboracion');
	}

}
