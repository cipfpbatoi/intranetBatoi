<?php

namespace Intranet\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class PollResultsExport implements FromView
{
    private $poll;
    private $votes;
    private $options_numeric;

    public function __construct($poll, $votes, $options_numeric)
    {
        $this->poll = $poll;
        $this->votes = $votes;
        $this->options_numeric = $options_numeric;
    }

    public function view(): View
    {
        return view('poll.partials.resolts.excel', [
            'poll' => $this->poll,
            'votes' => $this->votes,
            'options_numeric' => $this->options_numeric,
        ]);
    }
}