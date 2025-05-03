<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cotxes', function (Blueprint $table) {
            $table->id();
            $table->string('matricula',8);
            $table->string('marca',80);
            $table->string('idProfesor', 10)->collation('utf8mb3_unicode_ci');;
            $table->foreign('idProfesor')
                ->references('dni')
                ->on('profesores')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['matricula', 'idProfesor']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cotxes');
    }
};
