<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const UNIQUE_INDEX = 'reuniones_curso_organo_numero_acta_unique';

    /**
     * Afig i inicialitza la numeració visible de les actes.
     */
    public function up(): void
    {
        Schema::create('reunion_acta_counters', function (Blueprint $table): void {
            $table->string('curso', 20);
            $table->string('organo', 100);
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->primary(['curso', 'organo']);
        });

        Schema::table('reuniones', function (Blueprint $table): void {
            $table->string('organo_acta', 100)->nullable()->after('curso');
            $table->unsignedInteger('numero_acta')->nullable()->after('organo_acta');
        });

        $this->backfill();

        Schema::table('reuniones', function (Blueprint $table): void {
            $table->unique(['curso', 'organo_acta', 'numero_acta'], self::UNIQUE_INDEX);
        });
    }

    /**
     * Elimina la numeració visible i els seus comptadors.
     */
    public function down(): void
    {
        Schema::table('reuniones', function (Blueprint $table): void {
            $table->dropUnique(self::UNIQUE_INDEX);
            $table->dropColumn(['organo_acta', 'numero_acta']);
        });

        Schema::dropIfExists('reunion_acta_counters');
    }

    /**
     * Numera les actes existents per data i identificador dins de cada context.
     */
    private function backfill(): void
    {
        $numbers = [];

        DB::table('reuniones')
            ->orderBy('fecha')
            ->orderBy('id')
            ->get()
            ->each(function (object $meeting) use (&$numbers): void {
                $course = (string) $meeting->curso;
                $organ = $this->legacyOrganKey($meeting);
                $number = ($numbers[$course][$organ] ?? 0) + 1;
                $numbers[$course][$organ] = $number;

                DB::table('reuniones')->where('id', $meeting->id)->update([
                    'organo_acta' => $organ,
                    'numero_acta' => $number,
                ]);
            });

        foreach ($numbers as $course => $organs) {
            foreach ($organs as $organ => $number) {
                DB::table('reunion_acta_counters')->insert([
                    'curso' => $course,
                    'organo' => $organ,
                    'ultimo_numero' => $number,
                ]);
            }
        }
    }

    /**
     * Resol l'òrgan de les actes anteriors a la migració.
     */
    private function legacyOrganKey(object $meeting): string
    {
        $type = (int) $meeting->tipo;
        $creatorId = (string) $meeting->idProfesor;

        if (in_array($type, [2, 4, 5, 6, 7, 9, 11, 12], true)) {
            $groupCode = trim((string) ($meeting->idGrupo ?? ''));
            if ($groupCode === '') {
                $groupCode = (string) (DB::table('grupos')
                    ->where('tutor', $creatorId)
                    ->orderBy('codigo')
                    ->value('codigo') ?? 'llegat-' . $creatorId);
            }

            return 'grup:' . $groupCode;
        }

        if (in_array($type, [0, 10], true)) {
            $department = DB::table('profesores')->where('dni', $creatorId)->value('departamento');

            return 'departament:' . ($department !== null ? (string) $department : 'llegat-' . $creatorId);
        }

        return match ($type) {
            1 => 'claustre',
            3 => 'grup-treball:' . (trim((string) ($meeting->grupo ?? '')) ?: 'llegat-' . $creatorId),
            8 => 'cocope',
            default => 'tipus:' . $type . ':' . $creatorId,
        };
    }
};
