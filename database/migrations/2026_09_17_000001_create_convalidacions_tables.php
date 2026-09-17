<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea les taules per a convalidacions: sollicituds i convalidacions individuals.
     */
    public function up(): void
    {
        Schema::create('sollicituds_convalidacions', function (Blueprint $table) {
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';
            $table->id();
            $table->string('alumno_id', 10);
            $table->enum('estat', ['pendent', 'aprovat', 'rebutjat', 'documents_requerits'])
                ->default('pendent');
            $table->timestamp('data_sol·licitud')->useCurrent();
            $table->timestamp('data_resolucio')->nullable();
            $table->text('observacions')->nullable();
            $table->timestamps();

            $table->foreign('alumno_id')->references('nia')->on('alumnos')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->index(['alumno_id', 'estat']);
            $table->index(['estat', 'data_sol·licitud']);
        });

        Schema::create('convalidacions', function (Blueprint $table) {
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('sollicitud_convalidacio_id');
            $table->string('modulo_id', 10)->nullable();
            $table->enum('tipus_convalidacio', ['mateix_centre', 'altre_centre', 'escola_idiomes', 'titol_universitari', 'titol_fp']);
            $table->unsignedBigInteger('cicle_formatiu_cursat_id')->nullable();
            $table->string('certificat_path', 255)->nullable();
            $table->boolean('certificat_autentic')->nullable();
            $table->enum('estat', ['pendent', 'aprovat', 'rebutjat'])
                ->default('pendent');
            $table->text('motiu_rebutj')->nullable();
            $table->timestamps();

            $table->foreign('sollicitud_convalidacio_id')->references('id')->on('sollicituds_convalidacions')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('modulo_id')->references('codigo')->on('moduls')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('cicle_formatiu_cursat_id')->references('id')->on('cicles_formatius_cursats')
                ->cascadeOnUpdate()->nullOnDelete();

            $table->index(['sollicitud_convalidacio_id', 'estat']);
            $table->index(['modulo_id', 'estat']);
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
