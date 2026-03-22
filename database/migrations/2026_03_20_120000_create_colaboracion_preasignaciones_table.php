<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Crea la taula de preassignacions d'alumnat a col·laboracions.
     */
    public function up()
    {
        Schema::create('colaboracion_preasignaciones', function (Blueprint $table) {
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';
            $table->id();
            $table->unsignedInteger('idColaboracion');
            $table->string('idAlumno', 8);
            $table->string('idProfesor', 10);
            $table->string('estado', 20)->default('proposta');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('idColaboracion')
                ->references('id')
                ->on('colaboraciones')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('idAlumno')
                ->references('nia')
                ->on('alumnos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('idProfesor')
                ->references('dni')
                ->on('profesores')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index(['idColaboracion', 'estado'], 'col_preasig_colab_estado_idx');
            $table->index(['idAlumno', 'estado'], 'col_preasig_alumno_estado_idx');
        });
    }

    /**
     * Elimina la taula de preassignacions.
     */
    public function down()
    {
        Schema::dropIfExists('colaboracion_preasignaciones');
    }
};
