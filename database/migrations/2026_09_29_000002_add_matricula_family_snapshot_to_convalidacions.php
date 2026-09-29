<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Afig les famílies ITACA als departaments i el context de matrícula a les peticions. */
    public function up(): void
    {
        Schema::table('departamentos', function (Blueprint $table): void {
            $table->string('familia_professional_val')->nullable();
            $table->string('familia_professional_cas')->nullable();
            $table->string('codigo_xml', 50)->nullable();
            $table->string('abreviatura_xml', 50)->nullable();
        });

        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->unsignedInteger('ciclo_matricula_id')->nullable();
            $table->string('ciclo_matricula_codigo', 50)->nullable();
            $table->string('ciclo_matricula_nombre_val')->nullable();
            $table->string('ciclo_matricula_nombre_cas')->nullable();
            $table->unsignedTinyInteger('departamento_matricula_id')->nullable();
            $table->string('familia_matricula_nombre_val')->nullable();
            $table->string('familia_matricula_nombre_cas')->nullable();
            $table->string('familia_matricula_codigo_xml', 50)->nullable();
            $table->string('familia_matricula_abreviatura_xml', 50)->nullable();
        });

        $families = [
            10 => ['3306172290', '039', 'HOTELERIA I TURISME', 'HOSTELERÍA Y TURISMO'],
            25 => ['3306167239', '150', 'SEGURETAT I MEDI AMBIENT', 'SEGURIDAD Y MEDIO AMBIENTE'],
            24 => ['3306169525', '190', 'INFORMÀTICA I COMUNICACIONS', 'INFORMÁTICA Y COMUNICACIONES'],
            5 => ['3306173908', '001', 'ADMINISTRACIÓ I GESTIÓ', 'ADMINISTRACIÓN Y GESTIÓN'],
            2 => ['3306170110', '143', 'SERVICIS SOCIOCULTURALS I A LA COMUNITAT', 'SERVICIOS SOCIOCULTURALES Y A LA COMUNIDAD'],
            6 => ['3306170441', '061', 'SANITAT', 'SANIDAD'],
            3 => ['3306168275', '130', 'IMATGE PERSONAL', 'IMAGEN PERSONAL'],
        ];

        foreach ($families as $departmentId => [$xmlCode, $xmlAbbreviation, $nameValencian, $nameSpanish]) {
            DB::table('departamentos')->where('id', $departmentId)->update([
                'codigo_xml' => $xmlCode,
                'abreviatura_xml' => $xmlAbbreviation,
                'familia_professional_val' => $nameValencian,
                'familia_professional_cas' => $nameSpanish,
            ]);
        }
    }

    /** Elimina els camps i dades de context afegits per esta migració. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table): void {
            $table->dropColumn([
                'ciclo_matricula_id',
                'ciclo_matricula_codigo',
                'ciclo_matricula_nombre_val',
                'ciclo_matricula_nombre_cas',
                'departamento_matricula_id',
                'familia_matricula_nombre_val',
                'familia_matricula_nombre_cas',
                'familia_matricula_codigo_xml',
                'familia_matricula_abreviatura_xml',
            ]);
        });

        Schema::table('departamentos', function (Blueprint $table): void {
            $table->dropColumn([
                'familia_professional_val',
                'familia_professional_cas',
                'codigo_xml',
                'abreviatura_xml',
            ]);
        });
    }
};
