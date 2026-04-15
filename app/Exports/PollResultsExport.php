<?php

namespace Intranet\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

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

    public function __construct(
        $poll,
        $votes,
        $options_numeric,
        $options_select,
        $hasVotes,
        $stats,
        $selectStats,
        $selectHasVotes
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

        return [
            new PollResultsSheet('poll.partials.resolts.excel_general', 'Resultats', $data),
            new PollResultsSheet('poll.partials.resolts.excel_departament', 'Departaments', $data),
            new PollResultsSheet('poll.partials.resolts.excel_grup', 'Grups', $data),
        ];
    }
}
