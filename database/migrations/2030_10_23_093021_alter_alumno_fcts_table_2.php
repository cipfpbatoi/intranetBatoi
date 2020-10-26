<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAlumnoFctsTable2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                
		
                Schema::table('alumno_fcts', function(Blueprint $table)
		{
                    $table->float('beca')->default(0);
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
                    $table->dropColumn('beca');
                });
	}

}
