<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAlumnoFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                
		
                Schema::table('alumno_fcts', function(Blueprint $table)
		{
                    $table->smallInteger('horas')->nullable();
                    $table->date('desde')->nullable();
                    $table->date('hasta')->nullable();
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
                    $table->dropColumn('desde');
                    $table->dropColumn('hasta');
                    $table->dropColumn('horas');
                });
	}

}
