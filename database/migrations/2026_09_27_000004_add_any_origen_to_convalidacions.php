<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Reinicia les dades no productives i incorpora l'any immutable d'aprovació. */
return new class extends Migration
{
    /** Elimina les proves existents i afig l'any, opcional només per a orígens externs. */
    public function up(): void
    {
        $documents = DB::table('convalidacions')
            ->whereNotNull('document_path')
            ->pluck('document_path')
            ->filter()
            ->values()
            ->all();

        if ($documents !== []) {
            Storage::disk('convalidacions')->delete($documents);
        }

        DB::table('convalidacions')->delete();
        DB::table('sollicituds_convalidacions')->delete();

        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->unsignedSmallInteger('any_origen')->nullable()->after('ciclo_origen_nombre');
        });
    }

    /** Elimina el camp; les dades de prova esborrades no són recuperables. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn('any_origen');
        });
    }
};
