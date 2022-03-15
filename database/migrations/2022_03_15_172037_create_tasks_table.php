<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('descripcion', 100);
			$table->date('vencimiento');
			$table->string('fichero', 100)->nullable();
			$table->string('enlace', 200)->nullable();
			$table->boolean('destinatario')->default(1);
			$table->boolean('informativa')->default(1);
			$table->string('action', 7)->nullable();
			$table->boolean('activa')->default(0);
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
		Schema::drop('tasks');
	}

}
