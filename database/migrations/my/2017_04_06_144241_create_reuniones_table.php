<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReunionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reuniones', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('tipo')->default(0);
            $table->string('grupo',6)->nullable();
            $table->string('curso');
            $table->string('numero',2)->nullable();
            $table->datetime('fecha');
            $table->string('descripcion',120);
            $table->text('objetivos')->nullable();
            $table->string('idProfesor', 10);
            $table->String('idEspacio',10);
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
        Schema::dropIfExists('reuniones');
    }
}
