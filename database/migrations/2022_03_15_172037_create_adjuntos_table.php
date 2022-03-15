<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdjuntosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('adjuntos', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('name', 100);
			$table->string('owner', 12);
			$table->string('referencesTo', 12)->nullable();
			$table->string('title', 100);
			$table->string('extension', 4);
			$table->integer('size')->default(0);
			$table->timestamps();
			$table->string('route', 60);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('adjuntos');
	}

}
