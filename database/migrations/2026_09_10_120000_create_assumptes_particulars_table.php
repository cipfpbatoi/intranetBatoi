<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea les peticions d'assumptes particulars separades de les faltes.
     */
    public function up(): void
    {
        Schema::create('assumptes_particulars', function (Blueprint $table): void {
            // Les claus llegades de profesores i faltas utilitzen utf8mb3.
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';
            $table->id();
            $table->string('idProfesor', 10);
            $table->date('data_gaudi');
            $table->string('curs', 9);
            $table->enum('tipus', ['lectiu', 'no_lectiu']);
            $table->enum('torn', ['mati', 'vesprada', 'ambdos', 'sense_docencia']);
            $table->enum('estat', ['pendent', 'autoritzada', 'denegada', 'cancel_lada'])
                ->default('pendent');
            $table->text('motivacio_excepcional')->nullable();
            $table->text('pla_activitats')->nullable();
            $table->text('resolucio')->nullable();
            $table->timestamp('sollicitada_at')->nullable();
            $table->timestamp('resolta_at')->nullable();
            $table->timestamp('cancel_lada_at')->nullable();
            $table->string('resolta_per', 10)->nullable();
            $table->unsignedInteger('falta_id')->nullable();
            $table->timestamps();

            $table->foreign('idProfesor')->references('dni')->on('profesores')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('resolta_per')->references('dni')->on('profesores')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('falta_id')->references('id')->on('faltas')
                ->cascadeOnUpdate()->nullOnDelete();

            $table->index(['data_gaudi', 'estat']);
            $table->index(['data_gaudi', 'torn', 'estat']);
            $table->index(['idProfesor', 'curs', 'tipus', 'estat'], 'assumptes_saldo_index');
        });
    }

    /**
     * Elimina les peticions d'assumptes particulars.
     */
    public function down(): void
    {
        Schema::dropIfExists('assumptes_particulars');
    }
};
