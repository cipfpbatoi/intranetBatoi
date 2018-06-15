<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createEspacios extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('espacios', function (Blueprint $table) {
            $table->string('aula', 10)->primary();
            $table->string('descripcion', 50);
            $table->tinyInteger('departamento')->references('id')->on('departamentos');
            $table->string('gMati', 5)->nullable();
            $table->string('gVesprada', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('espacios');
    }

}
