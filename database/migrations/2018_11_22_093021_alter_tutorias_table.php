<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTutoriasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                
		
                Schema::table('tutorias', function(Blueprint $table)
		{
                    $table->tinyInteger('tipo')->default(0);
                });
                

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('alumno_fcts', function(Blueprint $table)
		{
                    $table->dropColumn('tipo');
                });
	}

}
