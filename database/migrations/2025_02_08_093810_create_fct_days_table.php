<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fct_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('alumno_fct_id');
            $table->foreign('alumno_fct_id')->references('id')->on('alumno_fcts')->onDelete('cascade');
            $table->date('dia');
            $table->decimal('hores_previstes', 4, 2);
            $table->decimal('hores_realitzades', 4, 2)->default(0);
            $table->text('descripcio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fct_days');
    }
};