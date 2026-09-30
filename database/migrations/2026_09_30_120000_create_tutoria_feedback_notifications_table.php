<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Crea el registre idempotent d'avisos de feedback.
     */
    public function up(): void
    {
        if (Schema::hasTable('tutoria_feedback_notifications')) {
            $this->repairPartialTable();
            return;
        }

        Schema::create('tutoria_feedback_notifications', function (Blueprint $table): void {
            // grupos.codigo i tutorias_grupos.idGrupo utilitzen el charset llegat.
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';

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
     * Completa sense perdre dades una taula que MySQL haja deixat a mig crear.
     */
    private function repairPartialTable(): void
    {
        DB::statement(
            'ALTER TABLE tutoria_feedback_notifications '
            . 'MODIFY idGrupo VARCHAR(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL'
        );

        $foreignKeys = collect(Schema::getForeignKeys('tutoria_feedback_notifications'))
            ->pluck('name');

        Schema::table('tutoria_feedback_notifications', function (Blueprint $table) use ($foreignKeys): void {
            if (!$foreignKeys->contains('tutoria_feedback_notifications_idtutoria_foreign')) {
                $table->foreign('idTutoria')->references('id')->on('tutorias')->cascadeOnDelete();
            }

            if (!$foreignKeys->contains('tutoria_feedback_notifications_idgrupo_foreign')) {
                $table->foreign('idGrupo')->references('codigo')->on('grupos')
                    ->cascadeOnDelete()->cascadeOnUpdate();
            }
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
