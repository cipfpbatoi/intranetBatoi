<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMaterialesBajaTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('materiales_baja', function (Blueprint $table)
		{
            $table->id();
            $table->integer('idMaterial')->unsigned()->index('materiales_foreign');
            $table->string('idProfesor', 10)->collation('utf8_unicode_ci')->nullable();
            $table->tinyInteger('tipo')->nullable()->default(0);
            $table->string('nuevoEstado', 10)->nullable();
            $table->string('motivo', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default(0);
            $table->timestamps();
            $table->foreign('idProfesor')
                ->references('dni')
                ->on('profesores')
                ->onUpdate('CASCADE')
                ->onDelete('NO ACTION');
            $table->foreign('idMaterial')->references('id')->on('materiales')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('materiales_baja');
	}

}
