<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterInstructorFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                
		Schema::table('instructor_fcts', function(Blueprint $table)
		{
                    $table->dropForeign('instructor_fcts_idinstructor_foreign');
                });
                Schema::table('instructor_fcts', function(Blueprint $table)
		{
                    $table->string('idInstructor', 10)->change();
                    $table->foreign('idInstructor')->references('dni')->on('instructores')->onUpdate('CASCADE')->onDelete('RESTRICT');
		
		});
                Schema::rename('instructor_fcts','colaboradores');

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::rename('colaboradores','instructor_fcts');
                Schema::table('instructor_fcts', function(Blueprint $table)
		{
                    $table->dropForeign('instructor_fcts_idinstructor_foreign');
                });
		Schema::table('instructor_fcts', function(Blueprint $table)
		{
			$table->string('idInstructor', 100)->change();
                        $table->foreign('idInstructor')->references('dni')->on('instructores')->onUpdate('CASCADE')->onDelete('RESTRICT');
		
		});
                
	}

}
