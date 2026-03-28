<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fa obligatori el tutor de l'FCT d'alumnat i endureix la relació.
     */
    public function up(): void
    {
        $foreignExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'alumno_fcts')
            ->where('CONSTRAINT_NAME', 'alumno_fcts_idprofesor_foreign')
            ->exists();

        if ($foreignExists) {
            Schema::table('alumno_fcts', function (Blueprint $table): void {
                $table->dropForeign('alumno_fcts_idprofesor_foreign');
            });
        }

        DB::statement(
            'ALTER TABLE alumno_fcts MODIFY idProfesor VARCHAR(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL'
        );

        Schema::table('alumno_fcts', function (Blueprint $table): void {
            $table->foreign('idProfesor', 'alumno_fcts_idprofesor_foreign')
                ->references('dni')
                ->on('profesores')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Restaura la nul·labilitat i el comportament anterior de la clau forana.
     */
    public function down(): void
    {
        $foreignExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'alumno_fcts')
            ->where('CONSTRAINT_NAME', 'alumno_fcts_idprofesor_foreign')
            ->exists();

        if ($foreignExists) {
            Schema::table('alumno_fcts', function (Blueprint $table): void {
                $table->dropForeign('alumno_fcts_idprofesor_foreign');
            });
        }

        DB::statement(
            'ALTER TABLE alumno_fcts MODIFY idProfesor VARCHAR(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL'
        );

        Schema::table('alumno_fcts', function (Blueprint $table): void {
            $table->foreign('idProfesor', 'alumno_fcts_idprofesor_foreign')
                ->references('dni')
                ->on('profesores')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
