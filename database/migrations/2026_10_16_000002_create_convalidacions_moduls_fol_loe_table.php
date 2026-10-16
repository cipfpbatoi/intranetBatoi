<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea i inicialitza el catàleg editable de mòduls FOL LOE. */
    public function up(): void
    {
        Schema::create('convalidacions_moduls_fol_loe', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('codigo_normalitzat', 30)->unique();
            $table->string('cicle_cas', 255);
            $table->string('cicle_val', 255);
            $table->string('nivell', 2);
            $table->timestamps();
        });

        $registres = [
            ['0021', 'Educación Infantil (LOE)', 'Educació Infantil (LOE)', 'GS'],
            ['0049', 'Cocina y Gastronomía', 'Cuina i Gastronomia', 'GM'],
            ['0062', 'Emergencias Sanitarias', 'Emergències Sanitàries', 'GM'],
            ['0106', 'Farmacia y Parafarmacia', 'Farmàcia i Parafarmàcia', 'GM'],
            ['0157', 'Servicios en Restauración', 'Serveis en Restauració', 'GM'],
            ['0218', 'Atención a Personas en Situación de Dependencia', 'Atenció a persones en situació de dependència', 'GM'],
            ['0229', 'Sistemas Microinformáticos y Redes', 'Sistemes Microinformàtics i Xarxes', 'GM'],
            ['0346', 'Integración Social (LOE)', 'Integració Social (LOE)', 'GS'],
            ['0380', 'Administración de Sistemas Informáticos en Red', 'Administració de Sistemes Informàtics en Xarxa', 'GS'],
            ['0449', 'Gestión Administrativa (LOE)', 'Gestió Administrativa (LOE)', 'GM'],
            ['0493', 'Desarrollo de Aplicaciones Multiplataforma', 'Desenrotllament d’Aplicacions Multiplataforma', 'GS'],
            ['0506', 'Dirección de Cocina', 'Direcció de Cuina', 'GS'],
            ['0617', 'Desarrollo de Aplicaciones Web', 'Desenrotllament d’Aplicacions Web', 'GS'],
            ['0644', 'Estética y Belleza', 'Estètica i Bellesa', 'GM'],
            ['0658', 'Administración y Finanzas (LOE)', 'Administració i Finances (LOE)', 'GS'],
            ['0665', 'Asistencia a la Dirección', 'Assistència a la Direcció', 'GS'],
            ['0755', 'Estética Integral y Bienestar', 'Estètica Integral i Benestar', 'GS'],
            ['0851', 'Peluquería y Cosmética Capilar', 'Perruqueria i Cosmètica Capil·lar', 'GM'],
            ['1133', 'Animación Sociocultural y Turística', 'Animació Sociocultural i Turística', 'GS'],
            ['1356', 'Imagen para el Diagnóstico y Medicina Nuclear', 'Imatge per al Diagnòstic i Medicina Nuclear', 'GS'],
            ['1376', 'Laboratorio Clínico y Biomédico', 'Laboratori Clínic i Biomèdic', 'GS'],
            ['1556', 'Química y Salud Ambiental', 'Química i Salut Ambiental', 'GS'],
        ];

        DB::table('convalidacions_moduls_fol_loe')->insert(array_map(
            static fn (array $fila): array => [
                'codigo' => $fila[0],
                'codigo_normalitzat' => ltrim($fila[0], '0') ?: '0',
                'cicle_cas' => $fila[1],
                'cicle_val' => $fila[2],
                'nivell' => $fila[3],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $registres
        ));
    }

    /** Elimina només la taula introduïda per esta migració. */
    public function down(): void
    {
        Schema::dropIfExists('convalidacions_moduls_fol_loe');
    }
};
