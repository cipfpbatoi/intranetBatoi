<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSignaturesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('alumno_fcts', function (Blueprint $table) {
            $table->string('idSao')->index()->change();
        });

		Schema::create('signatures', function (Blueprint $table)
		{
            $table->id();
            $table->string('tipus', 2);
            $table->string('idProfesor', 10)->collation('utf8_unicode_ci');
            $table->string('idSao', 8)->collation('utf8mb4_unicode_ci');
            $table->string('sendTo', 60);
            $table->boolean('signed')->default(0);
            $table->timestamps();
            $table->foreign('idProfesor')
                ->references('dni')
                ->on('profesores')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('idSao')
                ->references('idSao')
                ->on('alumno_fcts')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });


	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('signatures');
	}

}
