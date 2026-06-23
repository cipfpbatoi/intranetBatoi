<?php

namespace Intranet\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Fulla Excel basada en una vista Blade.
 */
class PollResultsSheet implements FromView, WithTitle, WithColumnFormatting
{
    private $view;
    private $title;
    private $data;
    private $columnFormats;

    /**
     * @param array<string, string> $columnFormats
     */
    public function __construct(string $view, string $title, array $data, array $columnFormats = [])
    {
        $this->view = $view;
        $this->title = $title;
        $this->data = $data;
        $this->columnFormats = $columnFormats;
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

    /**
     * Defineix formats nadius d'Excel per columna.
     *
     * @return array<string, string>
     */
    public function columnFormats(): array
    {
        return $this->columnFormats;
    }
}
