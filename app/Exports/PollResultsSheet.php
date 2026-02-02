<?php

namespace Intranet\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PollResultsSheet implements FromView, WithTitle
{
    private $view;
    private $title;
    private $data;

    public function __construct(string $view, string $title, array $data)
    {
        $this->view = $view;
        $this->title = $title;
        $this->data = $data;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function title(): string
    {
        return $this->title;
    }
}
