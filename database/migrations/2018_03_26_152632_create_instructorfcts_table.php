<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstructorfctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructor_fcts', function (Blueprint $table) {
            $table->Integer('idFct')->unsigned();
            $table->string('idInstructor',100);
            $table->smallInteger('horas')->nullable();
            $table->foreign('idInstructor')->references('dni')->on('instructores')
                    ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('idFct')->references('id')->on('fcts')
                    ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['idFct','idInstructor']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instructor_fcts');
    }
}
