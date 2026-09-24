<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Crea el rol de coordinació i les sol·licituds públiques de confirmació.
     */
    public function up(): void
    {
        Schema::table('instructores', function (Blueprint $table): void {
            $table->boolean('coordinador')->default(false)->after('departamento');
        });

        Schema::create('empresa_data_confirmations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('empresa_id');
            $table->string('tutor_dni', 15);
            $table->string('recipient_email');
            $table->string('token_hash', 64)->unique();
            $table->json('colaboracion_ids');
            $table->json('centro_ids');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->index(['empresa_id', 'tutor_dni']);
        });
    }

    /**
     * Elimina les estructures creades per a la confirmació externa.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_data_confirmations');

        Schema::table('instructores', function (Blueprint $table): void {
            $table->dropColumn('coordinador');
        });
    }
};
