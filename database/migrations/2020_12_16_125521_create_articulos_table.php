<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticulosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articulos', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('descripcion', 200)->nullable();
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->string('identificacion', 35)->nullable();
			$table->tinyInteger('estado')->default(1);
			$table->string('espacio_id', 10)->collation('utf8_unicode_ci')->index('espacios');
			$table->smallInteger('unidades')->default(1);
			$table->date('fechaultimoinventario')->nullable();
			$table->date('fechabaja')->nullable();
			$table->integer('numeracionInventario')->nullable();
			$table->integer('lote_id')->unsigned()->nullable();
            $table->foreign('espacio_id')->references('aula')->on('espacios')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('lote_id')->references('id')->on('lotes')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
        Schema::drop('articulos');
	}

}
