<?php

namespace Intranet\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Exporta els resultats agregats d'una enquesta en diverses pestanyes Excel.
 */
class PollResultsExport implements WithMultipleSheets
{
    private $poll;
    private $votes;
    private $options_numeric;
    private $options_select;
    private $hasVotes;
    private $stats;
    private $selectStats;
    private $selectHasVotes;
    private $studentSelectGroups;

    public function __construct(
        $poll,
        $votes,
        $options_numeric,
        $options_select,
        $hasVotes,
        $stats,
        $selectStats,
        $selectHasVotes,
        array $studentSelectGroups = []
    )
    {
        $this->poll = $poll;
        $this->votes = $votes;
        $this->options_numeric = $options_numeric;
        $this->options_select = $options_select;
        $this->hasVotes = $hasVotes;
        $this->stats = $stats;
        $this->selectStats = $selectStats;
        $this->selectHasVotes = $selectHasVotes;
        $this->studentSelectGroups = $studentSelectGroups;
    }

    public function sheets(): array
    {
        $data = [
            'poll' => $this->poll,
            'votes' => $this->votes,
            'options_numeric' => $this->options_numeric,
            'options_select' => $this->options_select,
            'hasVotes' => $this->hasVotes,
            'stats' => $this->stats,
            'select_stats' => $this->selectStats,
            'select_hasVotes' => $this->selectHasVotes,
        ];

        $sheets = [
            new PollResultsSheet('poll.partials.resolts.excel_general', 'Resultats', $data),
            new PollResultsSheet('poll.partials.resolts.excel_departament', 'Departaments', $data),
            new PollResultsSheet(
                'poll.partials.resolts.excel_grup',
                'Grups',
                $data,
                $this->groupSheetColumnFormats()
            ),
        ];

        foreach ($this->studentSelectGroups as $sheetData) {
            $sheets[] = new PollResultsSheet(
                'poll.partials.resolts.excel_student_group',
                $this->groupSheetTitle($sheetData),
                ['sheet' => $sheetData]
            );
        }

        return $sheets;
    }

    /**
     * Genera el nom de la fulla "codi del grup + cicle".
     *
     * @param array<string, mixed> $sheetData
     */
    private function groupSheetTitle(array $sheetData): string
    {
        $group = $sheetData['group'];
        $cycleName = $group->Ciclo?->ciclo ?? $group->idCiclo ?? '';

        return trim($group->codigo . ' ' . $cycleName);
    }

    /**
     * Formata les columnes de mitjana de la pestanya Grups amb dos decimals.
     *
     * @return array<string, string>
     */
    private function groupSheetColumnFormats(): array
    {
        $formats = [];
        $firstAverageColumn = 4;
        $lastAverageColumn = $firstAverageColumn + $this->options_numeric->count() - 1;

        for ($column = $firstAverageColumn; $column <= $lastAverageColumn; $column++) {
            $formats[Coordinate::stringFromColumnIndex($column)] = NumberFormat::FORMAT_NUMBER_00;
        }

        return $formats;
    }
}
