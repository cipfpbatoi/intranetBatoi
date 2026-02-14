<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('projectes', function (Blueprint $table) {
            $table->increments('id');
            $table->char('idAlumne','8')->reference('nia')->on('alumnos');
            $table->char('grup','5')->reference('codigo')->on('grupos');
            $table->tinyInteger('estat')->default(0);
            $table->string('titol');
            $table->text('objectius');
            $table->text('resultats');
            $table->text('aplicacions');
            $table->text('recursos');
            $table->text('descripcio');
            $table->text('observacions')->nullable();
            $table->date('defensa')->nullable();
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
		Schema::drop('projectes');
	}

}
