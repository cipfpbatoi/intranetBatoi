<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fcts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idAlumno',8);
            $table->Integer('idColaboracion');
            $table->string('instructor',100);
            $table->string('dni',10);
            $table->tinyInteger('asociacion');
            $table->date('desde');
            $table->date('hasta');
            $table->smallInteger('horas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fcts');
    }
}
