<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Afig l'acreditació declarada separada dels resultats verificats per XML. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->boolean('acreditacio_alternativa')->default(false)->after('origen');
            $table->text('acreditacio_descripcio')->nullable()->after('acreditacio_alternativa');
        });
    }

    /** Retira els camps de l'acreditació alternativa. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn(['acreditacio_alternativa', 'acreditacio_descripcio']);
        });
    }
};
