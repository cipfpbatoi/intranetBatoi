<?php

namespace Intranet\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PollResultsExport implements WithMultipleSheets
{
    private $poll;
    private $votes;
    private $options_numeric;
    private $hasVotes;
    private $stats;

    public function __construct($poll, $votes, $options_numeric, $hasVotes, $stats)
    {
        $this->poll = $poll;
        $this->votes = $votes;
        $this->options_numeric = $options_numeric;
        $this->hasVotes = $hasVotes;
        $this->stats = $stats;
    }

    public function sheets(): array
    {
        $data = [
            'poll' => $this->poll,
            'votes' => $this->votes,
            'options_numeric' => $this->options_numeric,
            'hasVotes' => $this->hasVotes,
            'stats' => $this->stats,
        ];

        return [
            new PollResultsSheet('poll.partials.resolts.excel_general', 'Resultats', $data),
            new PollResultsSheet('poll.partials.resolts.excel_departament', 'Departaments', $data),
            new PollResultsSheet('poll.partials.resolts.excel_grup', 'Grups', $data),
        ];
    }
}
