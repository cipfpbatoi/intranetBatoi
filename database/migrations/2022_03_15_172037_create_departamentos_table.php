<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartamentosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('departamentos', function(Blueprint $table)
		{
			$table->boolean('id')->primary();
			$table->string('cliteral', 30);
			$table->string('vliteral', 30);
			$table->string('depcurt', 3);
			$table->boolean('didactico')->default(1);
			$table->string('idProfesor', 10)->nullable()->index('departamentos_idprofesor_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('departamentos');
	}

}
