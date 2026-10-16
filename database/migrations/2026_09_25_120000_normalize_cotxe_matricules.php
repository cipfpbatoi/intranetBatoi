<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Cotxe;

return new class extends Migration
{
    /**
     * Normalitza les matrícules existents sense eliminar possibles duplicats.
     */
    public function up(): void
    {
        $cotxes = DB::table('cotxes')
            ->orderBy('id')
            ->get(['id', 'matricula', 'idProfesor']);
        $accessos = DB::table('cotxe_accessos')
            ->orderBy('id')
            ->get(['id', 'matricula']);

        $seen = [];

        foreach ($cotxes as $cotxe) {
            $normalized = Cotxe::normalizeMatricula($cotxe->matricula);
            $key = (string) $cotxe->idProfesor . "\0" . $normalized;

            if (isset($seen[$key])) {
                throw new RuntimeException(
                    "No es poden normalitzar les matrícules: els cotxes {$seen[$key]} i {$cotxe->id} quedarien duplicats."
                );
            }

            $seen[$key] = $cotxe->id;
        }

        DB::transaction(static function () use ($cotxes, $accessos): void {
            foreach ($cotxes as $cotxe) {
                DB::table('cotxes')
                    ->where('id', $cotxe->id)
                    ->update([
                        'matricula' => Cotxe::normalizeMatricula($cotxe->matricula),
                    ]);
            }

            foreach ($accessos as $acces) {
                DB::table('cotxe_accessos')
                    ->where('id', $acces->id)
                    ->update([
                        'matricula' => Cotxe::normalizeMatricula($acces->matricula),
                    ]);
            }
        });
    }

    /**
     * La retirada de separadors i el canvi a majúscules no són reversibles.
     */
    public function down(): void
    {
        // No es pot reconstruir de manera fiable el format original.
    }
};
