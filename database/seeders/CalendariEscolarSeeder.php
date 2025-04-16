<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Intranet\Entities\CalendariEscolar;
use Carbon\Carbon;

class CalendariEscolarSeeder extends Seeder
{
    public function run()
    {
        // Definir el període del curs escolar
        $iniciCurs = Carbon::parse('2024-09-01');
        $fiCurs = Carbon::parse('2025-07-31');

        // Període lectiu definit pel calendari (del 9 de setembre al 18 de juny)
        $iniciLectiu = Carbon::parse('2024-09-07');
        $fiLectiu = Carbon::parse('2025-06-21');

        // Festius oficials segons el PDF
        $festius = [
            '2024-10-09' => 'Dia de la Comunitat Valenciana',
            '2024-12-06' => 'Dia de la Constitució',
            '2024-12-25' => 'Nadal',
            '2025-01-01' => 'Cap d\'Any',
            '2025-03-17' => 'Falles',
            '2025-03-18' => 'Falles',
            '2025-05-02' => 'Pont',
            '2025-05-05' => 'Festiu Local',
            '2025-05-06' => 'Festiu Local',
        ];

        // Esdeveniments especials segons el PDF
        $esdeveniments = [
            '2024-09-09' => 'Inici de curs',
            '2025-06-18' => 'Final de curs',
            '2024-10-02' => 'Avaluació Inicial',
            '2024-11-25' => '1ª Avaluació (grups de segon)',
            '2024-11-27' => '1ª Avaluació (grups de primer)',
            '2025-02-19' => 'Exàmens Ordinària 2ons',
            '2025-02-26' => 'Avaluació Final Segons',
            '2025-03-05' => '2ª Avaluació Primers',
            '2025-04-30' => 'Final ordinària FP Bàsica',
            '2025-05-19' => 'Exàmens Ordinària Presencials',
            '2025-06-09' => 'Exàmens Ordinària Semi-presencials',
            '2025-06-19' => 'Exàmens Extraordinaris',
        ];

        // Definir períodes de vacances
        $vacances = [
            ['inici' => '2024-12-23', 'fi' => '2025-01-06', 'nom' => 'Vacances de Nadal'],
            ['inici' => '2025-04-17', 'fi' => '2025-04-27', 'nom' => 'Vacances de Pasqua'],
        ];

        // Recórrer tots els dies del curs i inserir-los a la base de dades
        while ($iniciCurs->lte($fiCurs)) {
            $data = $iniciCurs->toDateString();
            $tipus = 'no lectiu'; // Per defecte és no lectiu

            // Si és dins del període lectiu, el marquem com a lectiu
            if ($iniciCurs->between($iniciLectiu, $fiLectiu)) {
                $tipus = 'lectiu';
            }

            // Si és cap de setmana, és festiu
            if ($iniciCurs->isWeekend()) {
                $tipus = 'festiu';
            }

            // Si està en la llista de festius oficials, es marca com a festiu
            if (isset($festius[$data])) {
                $tipus = 'festiu';
            }

            // Si el dia es troba en un període de vacances, ho gestionem
            foreach ($vacances as $vac) {
                $iniciVacances = Carbon::parse($vac['inici']);
                $fiVacances = Carbon::parse($vac['fi']);

                if ($iniciCurs->between($iniciVacances, $fiVacances)) {
                    if (!isset($festius[$data]) && !$iniciCurs->isWeekend()) {
                        $tipus = 'no lectiu';
                    }
                }
            }

            // Esdeveniment si n'hi ha
            $esdeveniment = $esdeveniments[$data] ?? null;

            // Inserir a la base de dades
            CalendariEscolar::updateOrCreate(
                ['data' => $data],
                ['tipus' => $tipus, 'esdeveniment' => $esdeveniment]
            );

            // Passar al següent dia
            $iniciCurs->addDay();
        }
    }
}