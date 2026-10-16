<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /** Afig la localitat editable i identifica de manera estable els documents privats. */
    public function up(): void
    {
        Schema::table('profesores', function (Blueprint $table): void {
            $table->string('localitat', 100)->nullable();
        });
        Schema::table('documentos', function (Blueprint $table): void {
            $table->string('propietario_dni', 10)->nullable()->index();
        });

        // Recupera resolucions ja firmades abans que desapareguen les dades del curs.
        DB::table('assumptes_particulars')
            ->join('profesores', 'profesores.dni', '=', 'assumptes_particulars.idProfesor')
            ->where('assumptes_particulars.estat', 'autoritzada')
            ->whereNotNull('assumptes_particulars.resolucio_document')
            ->select([
                'assumptes_particulars.id',
                'assumptes_particulars.idProfesor',
                'assumptes_particulars.data_gaudi',
                'assumptes_particulars.curs',
                'assumptes_particulars.resolucio_document',
                'profesores.nombre',
                'profesores.apellido1',
                'profesores.apellido2',
            ])
            ->orderBy('assumptes_particulars.id')
            ->chunk(100, function ($peticions): void {
                foreach ($peticions as $peticio) {
                    if (!Storage::disk('local')->exists($peticio->resolucio_document)) {
                        continue;
                    }
                    if (DB::table('documentos')->where('fichero', $peticio->resolucio_document)->exists()) {
                        continue;
                    }
                    DB::table('documentos')->insert([
                        'tipoDocumento' => 'AssumpteParticular',
                        'curso' => $peticio->curs,
                        'propietario' => trim(ucwords(mb_strtolower(
                            $peticio->nombre . ' ' . $peticio->apellido1 . ' ' . $peticio->apellido2,
                            'UTF-8'
                        ))),
                        'propietario_dni' => $peticio->idProfesor,
                        'descripcion' => 'Resolució d’assumpte particular del ' . date('d/m/Y', strtotime($peticio->data_gaudi)),
                        'fichero' => $peticio->resolucio_document,
                        'rol' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    /** Retira els camps afegits sense eliminar els documents arxivats. */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table): void {
            $table->dropColumn('propietario_dni');
        });
        Schema::table('profesores', function (Blueprint $table): void {
            $table->dropColumn('localitat');
        });
    }
};
