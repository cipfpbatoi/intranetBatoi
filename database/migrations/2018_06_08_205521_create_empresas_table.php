<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmpresasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('empresas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('cif', 20)->nullable()->unique();
			$table->integer('concierto')->nullable();
			$table->string('nombre', 100);
			$table->string('email', 60)->nullable();
			$table->string('direccion', 100);
			$table->string('localidad', 30);
			$table->string('telefono', 20);
			$table->boolean('dual')->default(0);
			$table->string('actividad', 100)->nullable();
			$table->boolean('delitos')->default(0);
			$table->boolean('menores')->default(0);
			$table->text('observaciones', 65535)->nullable();
			$table->boolean('sao')->default(1);
			$table->boolean('copia_anexe1')->default(1);
                        $table->string('creador', 10)->nullable();
			$table->boolean('europa')->default(0);
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
		Schema::drop('empresas');
	}

}
