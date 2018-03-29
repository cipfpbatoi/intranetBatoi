<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createDocumentosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipoDocumento');
            $table->string('curso');
            $table->Integer('idDocumento')->nullable();
            $table->string('propietario',100)->nullable();
            $table->string('supervisor',100)->nullable();
            $table->string('descripcion', 100);
            $table->string('ciclo',100)->nullable();
            $table->string('modulo',100)->nullable();
            $table->string('grupo',100)->nullable();
            $table->string('fichero', 100)->nullable();
            $table->string('tags',255)->nullable();
            $table->Integer('rol')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('documentos');
    }

}

