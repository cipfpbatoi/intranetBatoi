<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAlumnoFctsTable1 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                
		
                Schema::table('alumno_fcts', function(Blueprint $table)
		{
                    $table->boolean('correoAlumno')->default(0);
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
                    $table->dropColumn('correoAlumno');
                    
                });
	}

}
