<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMunicipiosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('municipios', function(Blueprint $table)
		{
			$table->string('provincias_id', 2)->index('fk_municipios_provincias1_idx');
			$table->string('cod_municipio', 4);
			$table->string('municipio', 60);
			$table->primary(['provincias_id','cod_municipio']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('municipios');
	}

}
