<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Recupera la data d'impressió històrica a partir del document vinculat.
 */
return new class extends Migration {
    /**
     * Completa només dates buides amb la data real de creació del document.
     */
    public function up(): void
    {
        DB::table('actividades')
            ->whereNull('fecha_impresion')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')->from('documentos')
                    ->whereColumn('documentos.id', 'actividades.idDocumento')
                    ->whereNotNull('documentos.created_at');
            })
            ->update([
                'fecha_impresion' => DB::raw(
                    '(SELECT DATE(documentos.created_at) FROM documentos WHERE documentos.id = actividades.idDocumento)'
                ),
            ]);
    }

    /**
     * Conserva les dates recuperades: no es poden distingir de les originals.
     */
    public function down(): void
    {
        // No eliminem informació històrica vàlida en revertir la migració.
    }
};
