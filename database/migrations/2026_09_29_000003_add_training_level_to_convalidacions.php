<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Afig instantànies del tipus/normativa de matrícula i del nivell d'origen. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->unsignedTinyInteger('ciclo_matricula_tipo')->nullable();
            $table->string('ciclo_matricula_tipo_nombre_val')->nullable();
            $table->string('ciclo_matricula_tipo_nombre_cas')->nullable();
            $table->string('ciclo_matricula_normativa', 20)->nullable();
            $table->string('nivel_origen_codigo', 50)->nullable();
            $table->string('nivel_origen_nombre_val')->nullable();
            $table->string('nivel_origen_nombre_cas')->nullable();
        });
    }

    /** Elimina els camps del nivell formatiu afegits per esta migració. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn([
                'ciclo_matricula_tipo',
                'ciclo_matricula_tipo_nombre_val',
                'ciclo_matricula_tipo_nombre_cas',
                'ciclo_matricula_normativa',
                'nivel_origen_codigo',
                'nivel_origen_nombre_val',
                'nivel_origen_nombre_cas',
            ]);
        });
    }
};
