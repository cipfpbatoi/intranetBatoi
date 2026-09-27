<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Substituïx el cicle origen per una còpia immutable del mòdul aprovat. */
return new class extends Migration
{
    /** Afig la còpia acadèmica i conserva el literal de cicle existent quan és possible. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->string('modulo_origen_codigo', 50)->nullable()->after('origen');
            $table->string('modulo_origen_nombre')->nullable()->after('modulo_origen_codigo');
            $table->string('ciclo_origen_codigo', 50)->nullable()->after('modulo_origen_nombre');
            $table->string('ciclo_origen_nombre')->nullable()->after('ciclo_origen_codigo');
            $table->decimal('nota_origen', 5, 2)->nullable()->after('ciclo_origen_nombre');
            $table->string('convocatoria_origen', 40)->nullable()->after('nota_origen');
        });

        if (Schema::hasColumn('convalidacions', 'ciclo_origen_id')) {
            DB::table('convalidacions')
                ->whereNotNull('ciclo_origen_id')
                ->orderBy('id')
                ->each(function (object $peticio): void {
                    $cicle = DB::table('ciclos')->where('id', $peticio->ciclo_origen_id)->first();
                    if ($cicle) {
                        DB::table('convalidacions')->where('id', $peticio->id)->update([
                            'ciclo_origen_codigo' => $cicle->ciclo ?? null,
                            'ciclo_origen_nombre' => $cicle->vliteral ?? $cicle->cliteral ?? null,
                        ]);
                    }
                });

            Schema::table('convalidacions', function (Blueprint $table): void {
                $table->dropForeign(['ciclo_origen_id']);
                $table->dropColumn('ciclo_origen_id');
            });
        }
    }

    /** Restaura el camp tècnic anterior sense inventar una equivalència amb la còpia. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->integer('ciclo_origen_id')->nullable()->after('origen');
            $table->foreign('ciclo_origen_id')->references('id')->on('ciclos')
                ->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn([
                'modulo_origen_codigo',
                'modulo_origen_nombre',
                'ciclo_origen_codigo',
                'ciclo_origen_nombre',
                'nota_origen',
                'convocatoria_origen',
            ]);
        });
    }
};
