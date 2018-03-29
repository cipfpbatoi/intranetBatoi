<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createTiposincidencias extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tipoincidencias', function (Blueprint $table) {
            $table->tinyInteger('id')->primary();
            $table->string('nombre', 30);
            $table->string('nom',30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('tipoincidencias');
    }

}
