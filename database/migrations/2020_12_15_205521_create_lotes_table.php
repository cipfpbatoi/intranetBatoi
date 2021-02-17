<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lotes', function(Blueprint $table)
		{
            $table->string('registre',12)->primary();
            $table->boolean('procedencia')->nullable();
			$table->string('proveedor', 90)->nullable();
			$table->date('fechaAlta')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lotes');
	}

}
