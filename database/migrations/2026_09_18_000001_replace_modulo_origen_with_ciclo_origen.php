<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Substituïx el mòdul origen pel cicle cursat que s'aporta com a estudi previ. */
return new class extends Migration
{
    /** Migra les peticions existents i compacta els orígens acadèmics externs. */
    public function up(): void
    {
        if (!Schema::hasColumn('convalidacions', 'ciclo_origen_id')) {
            Schema::table('convalidacions', function (Blueprint $table): void {
                $table->integer('ciclo_origen_id')->nullable()->after('origen');
            });
        } else {
            DB::statement('ALTER TABLE `convalidacions` MODIFY `ciclo_origen_id` INT NULL');
        }

        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->foreign('ciclo_origen_id')->references('id')->on('ciclos')
                ->cascadeOnUpdate()->nullOnDelete();
        });

        DB::table('convalidacions')
            ->whereIn('origen', ['certificat_academic', 'certificat_notes'])
            ->update(['origen' => 'altre_centre']);

        DB::table('convalidacions')
            ->where('origen', 'propi_centre')
            ->whereNotNull('modulo_origen_id')
            ->orderBy('id')
            ->each(function (object $peticio): void {
                $cicloId = DB::table('modulo_ciclos')
                    ->where('idModulo', $peticio->modulo_origen_id)
                    ->whereNotNull('idCiclo')
                    ->value('idCiclo');

                if ($cicloId) {
                    DB::table('convalidacions')->where('id', $peticio->id)->update(['ciclo_origen_id' => $cicloId]);
                }
            });

        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropForeign(['modulo_origen_id']);
            $table->dropColumn('modulo_origen_id');
        });
    }

    /** Restaura l'esquema anterior sense reconstruir una equivalència de mòdul. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->string('modulo_origen_id', 12)->nullable()->after('origen');
            $table->foreign('modulo_origen_id')->references('codigo')->on('modulos')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->dropForeign(['ciclo_origen_id']);
            $table->dropColumn('ciclo_origen_id');
        });
    }
};
