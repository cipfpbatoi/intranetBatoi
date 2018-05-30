<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programaciones', function (Blueprint $table) {
            $table->increments('id');
            //$table->string('idModulo',6);
            //$table->string('idProfesor',10);
            //$table->date('desde');
            //$table->date('hasta');
            $table->string('fichero');
            $table->tinyInteger('anexos')->default(0);
            $table->tinyInteger('estado')->default(0);
            $table->Integer('checkList')->default(0);
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
        Schema::dropIfExists('programaciones');
    }
}
