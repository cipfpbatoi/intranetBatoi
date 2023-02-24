<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErasmusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erasmus', function (Blueprint $table) {
            $table->string('idSao', 12)->primary();
            $table->string('name', 120);
            $table->string('email', 60)->nullable();
            $table->string('telefon', 20)->nullable();
            $table->string('direccio', 150)->nullable();
            $table->string('localidad', 60)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erasmus');
    }
}
