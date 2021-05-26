<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksProfesoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_profesores', function (Blueprint $table) {
            $table->bigInteger('id_task')->unsigned();
            $table->string('id_profesor', 10)->collation('utf8_unicode_ci');
            $table->boolean('check')->default(0);
            $table->boolean('valid')->default(0);
            $table->foreign('id_task')->references('id')->on('tasks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('id_profesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('tasks_profesores');
    }
}
