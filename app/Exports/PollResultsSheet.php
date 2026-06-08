<?php

namespace Intranet\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Fulla Excel basada en una vista Blade.
 */
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
        $title = str_replace(['\\', '/', '?', '*', ':', '[', ']'], ' ', trim($this->title));
        $title = preg_replace('/\s+/', ' ', $title) ?: 'Fulla';

        return mb_substr($title, 0, 31);
    }
}
