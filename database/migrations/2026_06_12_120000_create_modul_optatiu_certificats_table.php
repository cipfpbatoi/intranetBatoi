<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea les taules de metadades d'emissió de certificats de mòduls optatius.
     */
    public function up(): void
    {
        Schema::create('modul_optatiu_certificats', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('idModuloGrupo')->unique();
            $table->string('denominacio', 200);
            $table->string('idProfesor', 10)
                ->charset('utf8mb3')
                ->collation('utf8mb3_unicode_ci');
            $table->timestamps();

            $table->foreign('idModuloGrupo')
                ->references('id')
                ->on('modulo_grupos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('idProfesor')
                ->references('dni')
                ->on('profesores')
                ->cascadeOnUpdate();
        });

        Schema::create('modul_optatiu_certificat_alumnes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('idCertificat')
                ->constrained('modul_optatiu_certificats')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('idAlumno', 8)
                ->charset('utf8mb3')
                ->collation('utf8mb3_unicode_ci');
            $table->timestamp('enviat_at')->nullable();
            $table->timestamp('registrat_at')->nullable();
            $table->string('fitxer', 255)->nullable();
            $table->timestamps();

            $table->unique(['idCertificat', 'idAlumno']);
            $table->foreign('idAlumno')
                ->references('nia')
                ->on('alumnos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Elimina les taules de metadades d'emissió.
     */
    public function down(): void
    {
        Schema::dropIfExists('modul_optatiu_certificat_alumnes');
        Schema::dropIfExists('modul_optatiu_certificats');
    }
};
