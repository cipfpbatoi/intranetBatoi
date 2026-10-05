<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Afig les dades bilingües de l'origen acadèmic i la família professional. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->string('modulo_origen_nombre_val')->nullable();
            $table->string('modulo_origen_nombre_cas')->nullable();
            $table->string('ciclo_origen_nombre_val')->nullable();
            $table->string('ciclo_origen_nombre_cas')->nullable();
            $table->string('familia_professional_codigo', 50)->nullable();
            $table->string('familia_professional_nombre_val')->nullable();
            $table->string('familia_professional_nombre_cas')->nullable();
        });
    }

    /** Elimina els camps bilingües i de família afegits per esta migració. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn([
                'modulo_origen_nombre_val',
                'modulo_origen_nombre_cas',
                'ciclo_origen_nombre_val',
                'ciclo_origen_nombre_cas',
                'familia_professional_codigo',
                'familia_professional_nombre_val',
                'familia_professional_nombre_cas',
            ]);
        });
    }
};
