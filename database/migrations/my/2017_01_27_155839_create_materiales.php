<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createMateriales extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('materiales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nserieprov', 50)->nullable();
            $table->string('descripcion', 255);
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->tinyInteger('procedencia')->nullable();
            $table->tinyInteger('estado')->default(0);
            $table->string('espacio', 10);
            $table->smallInteger('unidades')->default(1);
            $table->string('ISBN', 35)->nullable();
            $table->date('fechaultimoinventario')->nullable();
            $table->date('fechabaja')->nullable();
            $table->smallInteger('tipo')->nullable();
            $table->String('proveedor',30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('materiales');
    }

}
