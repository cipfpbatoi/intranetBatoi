<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('grupos', function(Blueprint $table)
		{
			$table->string('codigo', 5)->primary();
			$table->string('nombre', 45);
			$table->string('turno', 1)->index('turno');
			$table->string('tutor', 10)->nullable()->index('tutor');
			$table->boolean('idCiclo')->nullable();
			$table->boolean('curso')->nullable();
			$table->boolean('acta_pendiente')->default(0);
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
		Schema::drop('grupos');
	}

}
