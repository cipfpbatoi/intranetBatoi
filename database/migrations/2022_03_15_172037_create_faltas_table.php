<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFaltasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('faltas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idProfesor', 10)->index('faltas_idprofesor_foreign');
			$table->date('desde');
			$table->date('hasta')->nullable();
			$table->string('motivos', 2);
			$table->string('observaciones', 200)->nullable();
			$table->boolean('estado')->default(0);
			$table->timestamps();
			$table->string('fichero', 100)->nullable();
			$table->time('hora_ini')->nullable();
			$table->time('hora_fin')->nullable();
			$table->boolean('dia_completo')->nullable();
			$table->boolean('baja')->nullable();
			$table->integer('idDocumento')->unsigned()->nullable()->index('faltas_iddocumento_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('faltas');
	}

}
