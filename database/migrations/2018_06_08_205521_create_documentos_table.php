<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documentos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('tipoDocumento');
			$table->string('curso', 10)->default('');
			$table->integer('idDocumento')->nullable();
			$table->string('propietario', 100)->nullable();
			$table->string('supervisor', 100)->nullable();
			$table->string('descripcion', 200);
			$table->string('ciclo', 100)->nullable();
			$table->string('modulo', 100)->nullable();
			$table->string('grupo', 100)->nullable();
			$table->string('fichero', 100)->nullable();
			$table->string('tags')->nullable();
			$table->integer('rol')->default(1);
			$table->string('enlace')->nullable();
			$table->text('detalle', 65535)->nullable();
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
		Schema::drop('documentos');
	}

}
