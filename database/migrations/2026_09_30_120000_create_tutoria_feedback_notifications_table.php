<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Crea el registre idempotent d'avisos de feedback.
     */
    public function up(): void
    {
        Schema::create('tutoria_feedback_notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('idTutoria');
            $table->string('idGrupo', 10);
            $table->date('week_start');
            $table->timestamps();

            $table->unique(
                ['idTutoria', 'idGrupo', 'week_start'],
                'tutoria_feedback_notification_unique'
            );
            $table->foreign('idTutoria')->references('id')->on('tutorias')->cascadeOnDelete();
            $table->foreign('idGrupo')->references('codigo')->on('grupos')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Elimina el registre d'avisos de feedback.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutoria_feedback_notifications');
    }
};
