<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Afig metadades d'auditoria i la reserva persistent de deduplicació.
     */
    public function up(): void
    {
        Schema::table('cotxe_accessos', function (Blueprint $table): void {
            $table->string('matricula_canonica')->nullable()->after('matricula');
            $table->string('idProfesor', 10)->nullable()->after('matricula_canonica');
            $table->string('resultat_resolucio', 20)->default('sense_candidat')->after('tipus');
            $table->unsignedTinyInteger('diferencia_ocr')->nullable()->after('resultat_resolucio');
            $table->unsignedTinyInteger('intents_domotica')->default(0)->after('diferencia_ocr');
            $table->string('error_domotica')->nullable()->after('intents_domotica');
        });

        Schema::create('cotxe_acces_reserves', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor', 10);
            $table->enum('tipus', ['entrada', 'eixida']);
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->unique(['idProfesor', 'tipus']);
        });
    }

    /**
     * Reverteix les estructures específiques de l'auditoria d'accessos.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotxe_acces_reserves');

        Schema::table('cotxe_accessos', function (Blueprint $table): void {
            $table->dropColumn([
                'matricula_canonica', 'idProfesor', 'resultat_resolucio',
                'diferencia_ocr', 'intents_domotica', 'error_domotica',
            ]);
        });
    }
};
