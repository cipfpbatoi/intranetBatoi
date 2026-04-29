<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Evita duplicats de guàrdies diàries per professor, data i sessió.
 */
return new class extends Migration
{
    private const INDEX_NAME = 'guardias_profesor_dia_hora_unique';

    /**
     * Executa la migració.
     *
     * @return void
     */
    public function up()
    {
        $this->removeDuplicateGuardias();

        Schema::table('guardias', function (Blueprint $table): void {
            $table->unique(['idProfesor', 'dia', 'hora'], self::INDEX_NAME);
        });
    }

    /**
     * Reverteix la migració.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guardias', function (Blueprint $table): void {
            $table->dropUnique(self::INDEX_NAME);
        });
    }

    /**
     * Elimina duplicats conservant el registre més recent de cada franja.
     *
     * @return void
     */
    private function removeDuplicateGuardias(): void
    {
        DB::statement(
            'DELETE FROM guardias
             WHERE id NOT IN (
                SELECT id FROM (
                    SELECT MAX(id) AS id
                    FROM guardias
                    GROUP BY idProfesor, dia, hora
                ) AS guardias_to_keep
             )'
        );
    }
};
