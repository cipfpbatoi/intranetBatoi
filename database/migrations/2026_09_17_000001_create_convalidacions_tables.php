<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea les capçaleres i les peticions individuals de convalidació. */
    public function up(): void
    {
        Schema::create('sollicituds_convalidacions', function (Blueprint $table) {
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';
            $table->id();
            $table->string('alumno_id', 8);
            $table->string('submission_token', 64);
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            $table->foreign('alumno_id')->references('nia')->on('alumnos')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['alumno_id', 'submission_token']);
            $table->index(['alumno_id', 'submitted_at']);
        });

        Schema::create('convalidacions', function (Blueprint $table) {
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('sollicitud_convalidacio_id');
            $table->string('modulo_destino_id', 12);
            $table->string('origen', 40);
            $table->string('modulo_origen_id', 12)->nullable();
            $table->string('document_path')->nullable();
            $table->string('document_original_name')->nullable();
            $table->string('document_mime', 100)->nullable();
            $table->boolean('declaracio_responsable')->default(false);
            $table->string('estat', 60)->default('en_proces');
            $table->text('observacions')->nullable();
            $table->string('revisat_per', 10)->nullable();
            $table->timestamp('revisat_at')->nullable();
            $table->timestamps();

            $table->foreign('sollicitud_convalidacio_id')->references('id')->on('sollicituds_convalidacions')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('modulo_destino_id')->references('codigo')->on('modulos')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('modulo_origen_id')->references('codigo')->on('modulos')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('revisat_per')->references('dni')->on('profesores')
                ->cascadeOnUpdate()->nullOnDelete();

            $table->index(['sollicitud_convalidacio_id', 'estat']);
            $table->index(['origen', 'estat']);
            $table->unique(['sollicitud_convalidacio_id', 'modulo_destino_id'], 'convalidacions_sollicitud_modul_unique');
        });
    }

    /**
     * Elimina les taules de convalidacions.
     */
    public function down(): void
    {
        Schema::dropIfExists('convalidacions');
        Schema::dropIfExists('sollicituds_convalidacions');
    }
};
