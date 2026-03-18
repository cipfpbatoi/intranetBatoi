<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Elimina la taula legacy de faltes ITACA ja fora de flux.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('faltas_itaca');
    }

    /**
     * Recupera l'esquema original mínim de la taula legacy.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::create('faltas_itaca', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->date('dia');
            $table->tinyInteger('sesion_orden');
            $table->tinyInteger('estado')->default(0);
            $table->boolean('enCentro')->default(false);
            $table->string('idGrupo', 12);
            $table->text('justificacion')->nullable();
            $table->unsignedInteger('idDocumento')->nullable();

            $table->foreign('idProfesor')
                ->references('dni')
                ->on('profesores')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('idGrupo')
                ->references('codigo')
                ->on('grupos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('idDocumento')
                ->references('id')
                ->on('documentos')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
