<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Afig un filtre opcional per cicle a les preguntes d'enquesta.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('options', 'idCiclo')) {
            Schema::table('options', function (Blueprint $table): void {
                $table->integer('idCiclo')->nullable()->after('choices');
            });
        }

        if ($this->isMysql()) {
            // `ciclos.id` és `INT` signat; si una execució anterior va deixar
            // `options.idCiclo` com a unsigned, corregim el tipus abans de la FK.
            DB::statement('ALTER TABLE `options` MODIFY `idCiclo` INT NULL');
        }

        if (!$this->foreignKeyExists('options', 'options_idciclo_foreign')) {
            Schema::table('options', function (Blueprint $table): void {
                $table->foreign('idCiclo', 'options_idciclo_foreign')
                    ->references('id')
                    ->on('ciclos')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('options', 'idCiclo')) {
            return;
        }

        if ($this->foreignKeyExists('options', 'options_idciclo_foreign')) {
            Schema::table('options', function (Blueprint $table): void {
                $table->dropForeign('options_idciclo_foreign');
            });
        }

        Schema::table('options', function (Blueprint $table): void {
            $table->dropColumn('idCiclo');
        });
    }

    /**
     * Comprova si estem executant sobre MySQL/MariaDB.
     */
    private function isMysql(): bool
    {
        return Schema::getConnection()->getDriverName() === 'mysql';
    }

    /**
     * Comprova si una clau forana concreta ja existix.
     */
    private function foreignKeyExists(string $table, string $constraint): bool
    {
        if (!$this->isMysql()) {
            return false;
        }

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }
};
