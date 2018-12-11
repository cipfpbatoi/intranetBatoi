<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTipoExpedientesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                
		
                Schema::table('tipo_expedientes', function(Blueprint $table)
		{
                    $table->boolean('informe')->default(false);
                });
                

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('tipo_expedientes', function(Blueprint $table)
		{
                    $table->dropColumn('informe');
                });
	}

}
