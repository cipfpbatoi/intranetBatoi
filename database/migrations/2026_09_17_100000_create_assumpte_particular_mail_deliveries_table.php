<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Registra els avisos per correu sense dependre de les taules temporals del curs. */
return new class extends Migration {
    /** Crea el registre persistent d'intents i resultats. */
    public function up(): void
    {
        Schema::create('assumpte_particular_mail_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->string('curs', 9);
            $table->unsignedBigInteger('peticio_id');
            $table->string('tipus', 20);
            $table->string('destinatari_dni', 10)->nullable();
            $table->string('destinatari_email')->nullable();
            $table->string('estat', 20)->default('pendent');
            $table->unsignedSmallInteger('intents')->default(0);
            $table->string('error', 80)->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['curs', 'peticio_id', 'tipus'], 'ap_mail_event_unique');
            $table->index(['estat', 'last_attempt_at']);
        });
    }

    /** Elimina només el registre de correus si es revertix la migració. */
    public function down(): void
    {
        Schema::dropIfExists('assumpte_particular_mail_deliveries');
    }
};
